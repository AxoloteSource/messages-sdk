<?php

namespace AxoloteSource\MessagesSdk\Clases;

use AxoloteSource\MessagesSdk\DTO\ReplicateUrl;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

/**
 * CRUD access to the WhatsApp replicate hook URLs.
 */
class ReplicateUrls extends AxMessagesBase
{
    private ?string $currentUrl = null;

    private ?string $currentMethod = null;

    public function __construct()
    {
        parent::__construct('/api/v1/whatsapp/replicate-urls');
    }

    /**
     * @return array<int, ReplicateUrl>
     */
    public function list(): array
    {
        try {
            $response = $this->get($this->url);

            if ($response->successful()) {
                return collect($response->json('data') ?? [])
                    ->filter(fn ($item) => is_array($item))
                    ->map(fn (array $item) => ReplicateUrl::fromArray($item))
                    ->values()
                    ->all();
            }

            $this->logError('Error al obtener las URLs de replicación', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener las URLs de replicación', ['exception' => $e]);
        }

        return [];
    }

    public function create(string $url, bool $isActive = true): ?ReplicateUrl
    {
        return $this->write(fn () => $this->post([
            'url' => $url,
            'is_active' => $isActive,
        ]), 'Error al crear la URL de replicación');
    }

    public function update(string|int $id, string $url, ?bool $isActive = null): ?ReplicateUrl
    {
        $payload = ['url' => $url];

        if ($isActive !== null) {
            $payload['is_active'] = $isActive;
        }

        return $this->write(fn () => $this->request('PUT', "{$this->url}/{$id}", $payload), 'Error al actualizar la URL de replicación');
    }

    public function toggle(string|int $id): ?ReplicateUrl
    {
        return $this->write(fn () => $this->request('PATCH', "{$this->url}/{$id}/toggle"), 'Error al alternar la URL de replicación');
    }

    public function destroy(string|int $id): bool
    {
        try {
            $response = $this->request('DELETE', "{$this->url}/{$id}");

            if ($response->status() === 204 || $response->successful()) {
                return true;
            }

            $this->logError('Error al eliminar la URL de replicación', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al eliminar la URL de replicación', ['exception' => $e]);
        }

        return false;
    }

    /**
     * @param  callable(): PromiseInterface|Response  $callback
     */
    protected function write(callable $callback, string $errorMessage): ?ReplicateUrl
    {
        try {
            $response = $callback();

            if ($response->successful()) {
                return ReplicateUrl::fromArray($response->json());
            }

            $this->logError($errorMessage, $response);
        } catch (\Exception $e) {
            logger()->error($errorMessage, ['exception' => $e]);
        }

        return null;
    }

    protected function logError(string $message, Response $response): void
    {
        if ($this->debugMode) {
            logger()->error($message, [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    protected function request(string $method, ?string $url = null, ?array $data = null): PromiseInterface|Response
    {
        $this->currentUrl = $url;
        $this->currentMethod = $method;

        return parent::request($method, $url, $data);
    }

    protected function fakeResponse(): array
    {
        if (($this->currentMethod ?? 'GET') === 'DELETE') {
            return [];
        }

        if (($this->currentMethod ?? 'GET') === 'GET') {
            return $this->fakeDataTable([$this->fakeItem()]);
        }

        return $this->fakeItem();
    }

    protected function fakeStatusCode(): int
    {
        return ($this->currentMethod ?? 'GET') === 'DELETE' ? 204 : 200;
    }

    /**
     * @return array<string, mixed>
     */
    private function fakeItem(): array
    {
        return [
            'id' => 1,
            'url' => 'https://api.ejemplo.com/webhook',
            'is_active' => true,
            'created_at' => '2021-01-01 00:00:00',
            'updated_at' => '2021-01-01 00:00:00',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed>
     */
    private function fakeDataTable(array $items): array
    {
        return [
            'current_page' => 1,
            'data' => $items,
            'columns' => [],
            'per_page' => 15,
            'total' => count($items),
        ];
    }
}
