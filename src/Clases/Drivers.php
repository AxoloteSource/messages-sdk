<?php

namespace AxoloteSource\MessagesSdk\Clases;

use AxoloteSource\MessagesSdk\DTO\Driver;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

/**
 * CRUD access to the messaging drivers.
 */
class Drivers extends AxMessagesBase
{
    private ?string $currentUrl = null;

    private ?string $currentMethod = null;

    public function __construct()
    {
        parent::__construct('/api/v1/drivers');
    }

    /**
     * @return array<int, Driver>
     */
    public function list(): array
    {
        try {
            $response = $this->get($this->url);

            if ($response->successful()) {
                return collect($response->json('data') ?? [])
                    ->filter(fn ($item) => is_array($item))
                    ->map(fn (array $item) => Driver::fromArray($item))
                    ->values()
                    ->all();
            }

            $this->logError('Error al obtener los drivers', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener los drivers', ['exception' => $e]);
        }

        return [];
    }

    public function show(int|string $id): ?Driver
    {
        try {
            $response = $this->get("{$this->url}/{$id}");

            if ($response->successful()) {
                return Driver::fromArray($response->json());
            }

            $this->logError('Error al obtener el driver', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener el driver', ['exception' => $e]);
        }

        return null;
    }

    public function create(string $name, string $configKey): ?Driver
    {
        return $this->write(fn () => $this->post([
            'name' => $name,
            'config_key' => $configKey,
        ]), 'Error al crear el driver');
    }

    public function update(int|string $id, string $name, string $configKey): ?Driver
    {
        return $this->write(
            fn () => $this->request('PUT', "{$this->url}/{$id}", [
                'name' => $name,
                'config_key' => $configKey,
            ]),
            'Error al actualizar el driver'
        );
    }

    public function destroy(int|string $id): bool
    {
        try {
            $response = $this->request('DELETE', "{$this->url}/{$id}");

            if ($response->status() === 204 || $response->successful()) {
                return true;
            }

            $this->logError('Error al eliminar el driver', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al eliminar el driver', ['exception' => $e]);
        }

        return false;
    }

    /**
     * @param  callable(): PromiseInterface|Response  $callback
     */
    protected function write(callable $callback, string $errorMessage): ?Driver
    {
        try {
            $response = $callback();

            if ($response->successful()) {
                return Driver::fromArray($response->json());
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

        if (($this->currentMethod ?? 'GET') === 'GET' && ! str_contains((string) $this->currentUrl, '/api/v1/drivers/')) {
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
            'id' => 1,
            'name' => 'Test Driver',
            'config_key' => 'test.driver',
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
