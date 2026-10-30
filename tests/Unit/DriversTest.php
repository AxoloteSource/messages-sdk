<?php

namespace AxoloteSource\MessagesSdk\Tests\Unit;

use AxoloteSource\MessagesSdk\AxMessages;
use AxoloteSource\MessagesSdk\Clases\AxMessagesBase;
use AxoloteSource\MessagesSdk\Clases\Drivers;
use AxoloteSource\MessagesSdk\DTO\Driver;
use AxoloteSource\MessagesSdk\Tests\TestCase;
use Illuminate\Http\Client\Factory as HttpFactory;

class DriversTest extends TestCase
{
    public function test_drivers_returns_instance(): void
    {
        $this->assertInstanceOf(Drivers::class, AxMessages::drivers());
    }

    public function test_list_returns_drivers_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $drivers = AxMessages::drivers()->list();

        $this->assertIsArray($drivers);
        $this->assertCount(1, $drivers);
        $this->assertInstanceOf(Driver::class, $drivers[0]);
        $this->assertSame('Test Driver', $drivers[0]->name);
        $this->assertSame('test.driver', $drivers[0]->configKey);
    }

    public function test_show_returns_driver_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $driver = AxMessages::drivers()->show(1);

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame(1, $driver->id);
    }

    public function test_create_returns_driver_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $driver = AxMessages::drivers()->create('New Driver', 'new.driver');

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('Test Driver', $driver->name);
    }

    public function test_update_returns_driver_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $driver = AxMessages::drivers()->update(1, 'Updated Driver', 'updated.driver');

        $this->assertInstanceOf(Driver::class, $driver);
    }

    public function test_destroy_returns_true_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $this->assertTrue(AxMessages::drivers()->destroy(1));
    }

    public function test_has_error_is_false_on_success(): void
    {
        AxMessages::fake(true);

        $client = AxMessages::drivers();
        $client->list();

        $this->assertFalse($client->hasError());
        $this->assertEmpty($client->getError());
    }

    public function test_has_error_is_true_when_response_fails(): void
    {
        AxMessages::fake(false);
        $this->fakeHttpResponse(
            'https://api.ejemplo.com/api/v1/drivers',
            ['message' => 'Server error'],
            500
        );

        $client = AxMessages::drivers();
        $drivers = $client->list();

        $this->assertSame([], $drivers);
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
