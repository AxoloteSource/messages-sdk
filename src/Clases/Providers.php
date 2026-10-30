<?php

namespace AxoloteSource\MessagesSdk\Clases;

use AxoloteSource\MessagesSdk\DTO\Provider;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

/**
 * CRUD access to the messaging providers.
 */
class Providers extends AxMessagesBase
{
    private ?string $currentUrl = null;

    private ?string $currentMethod = null;

    public function __construct()
    {
        parent::__construct('/api/v1/providers');
    }

    /**
     * @return array<int, Provider>
     */
    public function list(): array
    {
        try {
            $response = $this->get($this->url);

            if ($response->successful()) {
                return collect($response->json('data') ?? [])
                    ->filter(fn ($item) => is_array($item))
                    ->map(fn (array $item) => Provider::fromArray($item))
                    ->values()
                    ->all();
            }

            $this->logError('Error al obtener los proveedores', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener los proveedores', ['exception' => $e]);
        }

        return [];
    }

    public function show(string $id): ?Provider
    {
        try {
            $response = $this->get("{$this->url}/{$id}");

            if ($response->successful()) {
                return Provider::fromArray($response->json());
            }

            $this->logError('Error al obtener el proveedor', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener el proveedor', ['exception' => $e]);
        }

        return null;
    }

    /**
     * @param  array<int, int|string>  $channelIds
     * @param  array{name: string, driver_id: int, active?: bool, default?: bool, config?: array}  $providerConfig
     */
    public function create(string $name, array $channelIds, array $providerConfig, bool $active = true): ?Provider
    {
        return $this->write(fn () => $this->post($this->payload($name, $active, $channelIds, $providerConfig)), 'Error al crear el proveedor');
    }

    /**
     * @param  array<int, int|string>  $channelIds
     * @param  array{name: string, driver_id: int, active?: bool, default?: bool, config?: array}  $providerConfig
     */
    public function update(string $id, string $name, array $channelIds, array $providerConfig, bool $active = true): ?Provider
    {
        return $this->write(
            fn () => $this->request('PUT', "{$this->url}/{$id}", $this->payload($name, $active, $channelIds, $providerConfig)),
            'Error al actualizar el proveedor'
        );
    }

    public function destroy(string $id): bool
    {
        try {
            $response = $this->request('DELETE', "{$this->url}/{$id}");

            if ($response->status() === 204 || $response->successful()) {
                return true;
            }

            $this->logError('Error al eliminar el proveedor', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al eliminar el proveedor', ['exception' => $e]);
        }

        return false;
    }

    /**
     * @param  array<int, int|string>  $channelIds
     * @param  array{name: string, driver_id: int, active?: bool, default?: bool, config?: array}  $providerConfig
     * @return array<string, mixed>
     */
    private function payload(string $name, bool $active, array $channelIds, array $providerConfig): array
    {
        return [
            'name' => $name,
            'active' => $active,
            'channel_ids' => $channelIds,
            'provider_config' => [
                'name' => $providerConfig['name'],
                'driver_id' => $providerConfig['driver_id'],
                'active' => $providerConfig['active'] ?? true,
                'default' => $providerConfig['default'] ?? false,
                'config' => $providerConfig['config'] ?? [],
            ],
        ];
    }

    /**
     * @param  callable(): PromiseInterface|Response  $callback
     */
    protected function write(callable $callback, string $errorMessage): ?Provider
    {
        try {
            $response = $callback();

            if ($response->successful()) {
                return Provider::fromArray($response->json());
            }

            $this->logError($errorMessage, $response);
        } catch (\Exception $e) {
            logger()->error($errorMessage, ['exception' => $e]);
        }

        return null;
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

        if (($this->currentMethod ?? 'GET') === 'GET' && ! str_contains((string) $this->currentUrl, '/api/v1/providers/')) {
            return $this->fakeDataTable([$this->fakeItem()]);
        }

        return $this->fakeItem();
    }

    protected function fakeStatusCode(): int
    {
        return ($this->currentMethod ?? 'GET') === 'DELETE' ? 204 : 200;
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

    /**
     * @return array<string, mixed>
     */
    private function fakeItem(): array
    {
        return [
            'id' => '9a779148-73b8-44d5-89f5-7489291122a2',
            'name' => 'Test Provider',
            'active' => true,
            'channels' => [
                ['id' => 1, 'name' => 'WhatsApp'],
            ],
            'driver_name' => 'WhatsApp',
            'default' => true,
            'provider_config' => [
                'id' => '9a7b36f1-6385-467f-9b6d-478588899793',
                'name' => 'Test Provider Config',
                'driver_id' => 2,
                'driver_name' => 'WhatsApp',
                'active' => true,
                'default' => true,
                'config' => ['api' => 'https://example.test'],
            ],
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
