<?php

namespace AxoloteSource\MessagesSdk\Clases;

use AxoloteSource\MessagesSdk\DTO\Contact;
use AxoloteSource\MessagesSdk\DTO\IncomingMessage;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

/**
 * Read-only access to inbound WhatsApp messages (contacts + messages).
 */
class Incoming extends AxMessagesBase
{
    private ?string $currentUrl = null;

    private ?string $currentMethod = null;

    public function __construct()
    {
        parent::__construct('/api/v1/whatsapp/incoming');
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int, Contact>
     */
    public function listContacts(array $query = []): array
    {
        try {
            $response = $this->get($this->buildUrl('/api/v1/whatsapp/incoming/contacts', $query));

            if ($response->successful()) {
                return collect($response->json('data') ?? [])
                    ->filter(fn ($item) => is_array($item))
                    ->map(fn (array $item) => Contact::fromArray($item))
                    ->values()
                    ->all();
            }

            $this->logError('Error al obtener contactos entrantes de WhatsApp', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener contactos entrantes de WhatsApp', ['exception' => $e]);
        }

        return [];
    }

    public function showContact(string|int $contact): ?Contact
    {
        try {
            $response = $this->get($this->buildUrl("/api/v1/whatsapp/incoming/contacts/{$contact}"));

            if ($response->successful()) {
                return Contact::fromArray($response->json());
            }

            $this->logError('Error al obtener el contacto entrante de WhatsApp', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener el contacto entrante de WhatsApp', ['exception' => $e]);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int, IncomingMessage>
     */
    public function listMessages(string|int $contact, array $query = []): array
    {
        try {
            $response = $this->get($this->buildUrl("/api/v1/whatsapp/incoming/contacts/{$contact}/messages", $query));

            if ($response->successful()) {
                return collect($response->json('data') ?? [])
                    ->filter(fn ($item) => is_array($item))
                    ->map(fn (array $item) => IncomingMessage::fromArray($item))
                    ->values()
                    ->all();
            }

            $this->logError('Error al obtener los mensajes entrantes de WhatsApp', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener los mensajes entrantes de WhatsApp', ['exception' => $e]);
        }

        return [];
    }

    public function showMessage(string|int $message): ?IncomingMessage
    {
        try {
            $response = $this->get($this->buildUrl("/api/v1/whatsapp/incoming/messages/{$message}"));

            if ($response->successful()) {
                return IncomingMessage::fromArray($response->json());
            }

            $this->logError('Error al obtener el mensaje entrante de WhatsApp', $response);
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener el mensaje entrante de WhatsApp', ['exception' => $e]);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    protected function buildUrl(string $path, array $query = []): string
    {
        $url = config('axMessages.url').$path;

        if (! empty($query)) {
            $url .= '?'.http_build_query($query);
        }

        return $url;
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
        $url = $this->currentUrl ?? '';

        if (preg_match('#/contacts/[^/]+/messages#', $url)) {
            return $this->fakeDataTable([$this->fakeMessage()]);
        }

        if (str_contains($url, '/contacts/')) {
            return $this->fakeItem($this->fakeContact());
        }

        if (str_contains($url, '/messages/')) {
            return $this->fakeItem($this->fakeMessage());
        }

        return $this->fakeDataTable([$this->fakeContact()]);
    }

    /**
     * @return array<string, mixed>
     */
    private function fakeContact(): array
    {
        return [
            'id' => 1,
            'phone' => '+521234567890',
            'name' => 'Test User',
            'last_message_text' => 'Hello',
            'last_message_at' => '2021-01-01 00:00:00',
            'unread_count' => 2,
            'created_at' => '2021-01-01 00:00:00',
            'updated_at' => '2021-01-01 00:00:00',
            'messages_count' => 2,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function fakeMessage(): array
    {
        return [
            'id' => 1,
            'contact_id' => 1,
            'wa_message_id' => 'wamid.test123',
            'type' => 'text',
            'text' => 'Hello',
            'media' => null,
            'raw' => ['id' => 'wamid.test123'],
            'wa_timestamp' => 1717000000,
            'created_at' => '2021-01-01 00:00:00',
            'updated_at' => '2021-01-01 00:00:00',
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function fakeItem(array $item): array
    {
        return [
            'status' => 'OK',
            'message' => null,
            'data' => $item,
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
