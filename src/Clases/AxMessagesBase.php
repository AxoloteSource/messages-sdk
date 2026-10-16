<?php

namespace AxoloteSource\MessagesSdk\Clases;

use AxoloteSource\MessagesSdk\AxMessages;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

class AxMessagesBase
{
    protected ?string $token;

    protected string $url;

    protected bool $debugMode = false;

    protected bool $userAuthUserToken = false;

    protected array $headers = ['Accept' => 'application/json'];

    /**
     * Último error ocurrido durante la operación.
     */
    protected array $error = [];

    private PromiseInterface|Response $response;

    protected static HttpFactory $httpClient;

    public function __construct(protected string $uri)
    {
        $this->token = config('axMessages.token');
        $this->url = config('axMessages.url').$this->uri;
        $this->debugMode = config('axMessages.debug');
        if ($this->userAuthUserToken) {
            $this->setAuthUserToken();
        }

        if (! isset(self::$httpClient)) {
            self::$httpClient = new HttpFactory;
        }
    }

    protected function http(): PendingRequest
    {
        return self::$httpClient->withToken($this->token)
            ->withHeaders($this->headers);
    }

    protected function request(string $method, ?string $url = null, ?array $data = null): PromiseInterface|Response
    {
        $actualUrl = $url ?? $this->url;
        $this->error = [];

        if (AxMessages::isFake() || config('axMessages.disabled')) {
            $this->fake($actualUrl);
        }

        try {
            $this->response = $this->http()->{lcfirst($method)}($actualUrl, $data);
        } catch (\Throwable $e) {
            $this->captureError($e);

            throw $e;
        }

        if ($this->response instanceof Response && $this->response->failed()) {
            $this->captureError($this->response);
        }

        return $this->response;
    }

    protected function get(string $url = null): PromiseInterface|Response
    {
        return $this->request('GET', $url);
    }

    protected function post(?array $data = null): PromiseInterface|Response
    {
        return $this->request('POST', data: $data);
    }

    protected function put(?array $data = null): PromiseInterface|Response
    {
        return $this->request('PUT', data: $data);
    }

    protected function delete(?array $data = null): PromiseInterface|Response
    {
        return $this->request('DELETE', data: $data);
    }

    protected function response(): ?array
    {
        return $this->response->json();
    }

    /**
     * Indica si la última operación terminó con un error.
     */
    public function hasError(): bool
    {
        return ! empty($this->error);
    }

    /**
     * Retorna el último error ocurrido, o un arreglo vacío si no hubo error.
     */
    public function getError(): array
    {
        return $this->error;
    }

    /**
     * Registra el error de una respuesta fallida o de una excepción.
     */
    protected function captureError(\Throwable|Response $source): void
    {
        if ($source instanceof Response) {
            $payload = rescue(fn () => $source->json(), [], false);

            if (! is_array($payload)) {
                $this->error = [
                    'status' => 'error',
                    'data' => [],
                    'message' => $source->body(),
                ];

                return;
            }

            $data = $payload['data'] ?? $payload['errors'] ?? $payload;
            $message = ($payload['message'] ?? null) ?: $this->extractErrorMessage($data) ?: $source->body();

            $this->error = [
                'status' => 'error',
                'data' => $data,
                'message' => $message,
            ];

            return;
        }

        $this->error = [
            'status' => 'error',
            'data' => [],
            'message' => $source->getMessage(),
        ];
    }

    /**
     * Extrae el primer mensaje de error legible de un arreglo de errores.
     *
     * @param  array<int|string, mixed>  $data
     */
    protected function extractErrorMessage(array $data): ?string
    {
        foreach ($data as $value) {
            if (is_array($value)) {
                $message = $this->extractErrorMessage($value);

                if ($message !== null) {
                    return $message;
                }

                continue;
            }

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    public function activeDebugMode(): self
    {
        $this->debugMode = true;

        return $this;
    }

    public function setAuthUserToken($token = null): self
    {
        if ($token) {
            $this->token = $token;

            return $this;
        }

        $this->token = request()->bearerToken();

        return $this;
    }

    protected function fakeResponse(): array
    {
        return [];
    }

    protected function fakeStatusCode(): int
    {
        return 200;
    }

    private function fake(?string $url = null): void
    {
        self::$httpClient->fake([
            ($url ?? $this->url) => self::$httpClient->response(
                $this->fakeResponse(),
                $this->fakeStatusCode(),
                $this->headers
            ),
        ]);
    }
}
