<?php

namespace AxoloteSource\MessagesSdk\Tests\Unit;

use AxoloteSource\MessagesSdk\AxMessages;
use AxoloteSource\MessagesSdk\Clases\AxMessagesBase;
use AxoloteSource\MessagesSdk\Clases\ReplicateUrls;
use AxoloteSource\MessagesSdk\DTO\ReplicateUrl;
use AxoloteSource\MessagesSdk\Tests\TestCase;
use Illuminate\Http\Client\Factory as HttpFactory;

class ReplicateUrlsTest extends TestCase
{
    public function test_replicate_urls_returns_instance(): void
    {
        $this->assertInstanceOf(ReplicateUrls::class, AxMessages::replicateUrls());
    }

    public function test_list_returns_urls_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $urls = AxMessages::replicateUrls()->list();

        $this->assertIsArray($urls);
        $this->assertCount(1, $urls);
        $this->assertInstanceOf(ReplicateUrl::class, $urls[0]);
        $this->assertSame('https://api.ejemplo.com/webhook', $urls[0]->url);
        $this->assertTrue($urls[0]->isActive);
    }

    public function test_create_returns_url_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $url = AxMessages::replicateUrls()->create('https://example.com/hook', true);

        $this->assertInstanceOf(ReplicateUrl::class, $url);
        $this->assertSame('https://api.ejemplo.com/webhook', $url->url);
    }

    public function test_update_returns_url_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $url = AxMessages::replicateUrls()->update(1, 'https://example.com/hook', false);

        $this->assertInstanceOf(ReplicateUrl::class, $url);
    }

    public function test_toggle_returns_url_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $url = AxMessages::replicateUrls()->toggle(1);

        $this->assertInstanceOf(ReplicateUrl::class, $url);
    }

    public function test_destroy_returns_true_in_fake_mode(): void
    {
        AxMessages::fake(true);

        $this->assertTrue(AxMessages::replicateUrls()->destroy(1));
    }

    public function test_has_error_is_false_on_success(): void
    {
        AxMessages::fake(true);

        $client = AxMessages::replicateUrls();
        $client->list();

        $this->assertFalse($client->hasError());
        $this->assertEmpty($client->getError());
    }

    public function test_has_error_is_true_when_response_fails(): void
    {
        AxMessages::fake(false);
        $this->fakeHttpResponse(
            'https://api.ejemplo.com/api/v1/whatsapp/replicate-urls',
            ['message' => 'Server error'],
            500
        );

        $client = AxMessages::replicateUrls();
        $urls = $client->list();

        $this->assertSame([], $urls);
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
