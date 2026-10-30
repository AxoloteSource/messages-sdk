<?php

namespace AxoloteSource\MessagesSdk\Tests\Unit;

use AxoloteSource\MessagesSdk\AxMessages;
use AxoloteSource\MessagesSdk\Clases\AxMessagesBase;
use AxoloteSource\MessagesSdk\Clases\Providers;
use AxoloteSource\MessagesSdk\DTO\Provider;
use AxoloteSource\MessagesSdk\DTO\ProviderConfig;
use AxoloteSource\MessagesSdk\Tests\TestCase;
use Illuminate\Http\Client\Factory as HttpFactory;

class ProvidersTest extends TestCase
{
    public function test_providers_returns_instance(): void
    {
        $this->assertInstanceOf(Providers::class, AxMessages::providers());
    }

    public function test_list_returns_providers_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $providers = AxMessages::providers()->list();

        $this->assertIsArray($providers);
        $this->assertCount(1, $providers);
        $this->assertInstanceOf(Provider::class, $providers[0]);
        $this->assertSame('Test Provider', $providers[0]->name);
        $this->assertTrue($providers[0]->active);
        $this->assertInstanceOf(ProviderConfig::class, $providers[0]->providerConfig);
        $this->assertSame('Test Provider Config', $providers[0]->providerConfig->name);
    }

    public function test_show_returns_provider_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $provider = AxMessages::providers()->show('9a779148-73b8-44d5-89f5-7489291122a2');

        $this->assertInstanceOf(Provider::class, $provider);
        $this->assertSame('9a779148-73b8-44d5-89f5-7489291122a2', $provider->id);
    }

    public function test_create_returns_provider_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $provider = AxMessages::providers()->create(
            'New Provider',
            [1],
            [
                'name' => 'New Provider Config',
                'driver_id' => 2,
                'active' => true,
                'default' => true,
                'config' => ['api' => 'https://example.test'],
            ]
        );

        $this->assertInstanceOf(Provider::class, $provider);
        $this->assertSame('Test Provider', $provider->name);
    }

    public function test_update_returns_provider_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $provider = AxMessages::providers()->update(
            '9a779148-73b8-44d5-89f5-7489291122a2',
            'Updated Provider',
            [2],
            [
                'name' => 'Updated Config',
                'driver_id' => 1,
                'active' => true,
                'default' => false,
                'config' => [],
            ],
            active: false
        );

        $this->assertInstanceOf(Provider::class, $provider);
    }

    public function test_destroy_returns_true_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $this->assertTrue(AxMessages::providers()->destroy('9a779148-73b8-44d5-89f5-7489291122a2'));
    }

    public function test_has_error_is_false_on_success(): void
    {
        AxMessages::fake(true);

        $client = AxMessages::providers();
        $client->list();

        $this->assertFalse($client->hasError());
        $this->assertEmpty($client->getError());
    }

    public function test_has_error_is_true_when_response_fails(): void
    {
        AxMessages::fake(false);
        $this->fakeHttpResponse(
            'https://api.ejemplo.com/api/v1/providers',
            ['message' => 'Server error'],
            500
        );

        $client = AxMessages::providers();
        $providers = $client->list();

        $this->assertSame([], $providers);
        $this->assertTrue($client->hasError());
        $this->assertSame('error', $client->getError()['status']);
        $this->assertSame('Server error', $client->getError()['message']);
    }

    private function fakeHttpResponse(string $url, array $body, int $status): void
    {
        $factory = new HttpFactory;
        $factory->fake([
            $url => HttpFactory::response($body, $status, ['Accept' => 'application/json']),
        ]);

        $reflection = new \ReflectionClass(AxMessagesBase::class);
        $property = $reflection->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue(null, $factory);
    }
}
