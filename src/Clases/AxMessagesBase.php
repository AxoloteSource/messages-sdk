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

    protected function post(?array $data = null): PromiseInterface|Response
    {
        if (AxMessages::isFake() || config('axMessages.disabled')) {
            $this->fake();
        }

        $this->response = $this->http()->post("$this->url", $data);

        return $this->response;
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

    private function fake(): void
    {
        self::$httpClient->fake([
            $this->url => self::$httpClient->response(
                $this->fakeResponse(),
                200,
                $this->headers
            ),
        ]);
    }
}
