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

        if (AxMessages::isFake() || config('axMessages.disabled')) {
            $this->fake($actualUrl);
        }

        $this->response = $this->http()->{lcfirst($method)}($actualUrl, $data);

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
