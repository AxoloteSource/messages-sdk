<?php

namespace AxoloteSource\MessagesSdk\Clases;

use AxoloteSource\MessagesSdk\AxMessages;
use AxoloteSource\MessagesSdk\DTO\Message;
use AxoloteSource\MessagesSdk\DTO\PushNotification;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;

class SendPushNotification extends AxMessagesBase
{
    private ?string $currentUrl = null;

    private ?string $currentMethod = null;

    public function __construct()
    {
        parent::__construct('/api/v1/messages/push-notifications/send');
    }

    public function send(string $userListId, string $pushNotificationId): ?Message
    {
        try {
            $response = $this->post([
                'push_notification_id' => $pushNotificationId,
                'user_list_id' => $userListId,
            ]);

            if ($response->successful()) {
                return Message::fromArray($response->json());
            }

            if ($this->debugMode) {
                logger()->error('Error en el envió de Push Notification', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            logger()->error('Excepción en el envió de Push Notification', ['exception' => $e]);
        }

        return null;
    }

    public function create(string $title, string $body, ?array $data = null): ?PushNotification
    {
        try {
            $url = config('axMessages.url').'/api/v1/push-notifications';
            $payload = [
                'title' => $title,
                'body' => $body,
            ];

            if ($data !== null) {
                $payload['data'] = $data;
            }

            $response = $this->request('POST', $url, $payload);

            if ($response->successful()) {
                return PushNotification::fromArray($response->json());
            }

            if ($this->debugMode) {
                logger()->error('Error al crear Push Notification', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            logger()->error('Excepción al crear Push Notification', ['exception' => $e]);
        }

        return null;
    }

    public function show(string $id): ?PushNotification
    {
        try {
            $url = config('axMessages.url')."/api/v1/push-notifications/$id";
            $response = $this->get($url);

            if ($response->successful()) {
                return PushNotification::fromArray($response->json());
            }

            if ($this->debugMode) {
                logger()->error('Error al obtener Push Notification', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            logger()->error('Excepción al obtener Push Notification', ['exception' => $e]);
        }

        return null;
    }

    public function update(string $id, ?string $title = null, ?string $body = null, ?array $data = null): ?PushNotification
    {
        try {
            $url = config('axMessages.url')."/api/v1/push-notifications/{$id}";
            $payload = [];

            if ($title !== null) {
                $payload['title'] = $title;
            }

            if ($body !== null) {
                $payload['body'] = $body;
            }

            if ($data !== null) {
                $payload['data'] = $data;
            }

            $response = $this->request('PUT', $url, $payload);

            if ($response->successful()) {
                return PushNotification::fromArray($response->json());
            }

            if ($this->debugMode) {
                logger()->error('Error al actualizar Push Notification', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            logger()->error('Excepción al actualizar Push Notification', ['exception' => $e]);
        }

        return null;
    }

    public function destroy(string $id): bool
    {
        try {
            $url = config('axMessages.url')."/api/v1/push-notifications/{$id}";
            $response = $this->request('DELETE', $url);

            if ($response->status() === 204) {
                return true;
            }

            if ($this->debugMode) {
                logger()->error('Error al eliminar Push Notification', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return false;
        } catch (\Exception $e) {
            logger()->error('Excepción al eliminar Push Notification', ['exception' => $e]);
        }

        return false;
    }

    protected function request(string $method, ?string $url = null, ?array $data = null): PromiseInterface|Response
    {
        $this->currentUrl = $url;
        $this->currentMethod = $method;

        return parent::request($method, $url, $data);
    }

    protected function fakeResponse(): array
    {
        if ($this->currentUrl && (str_contains($this->currentUrl, '/api/v1/push-notifications') || str_contains($this->currentUrl, '/api/v1/messages/push-notifications'))) {
            return [
                'status' => 'OK',
                'message' => null,
                'data' => [
                    'id' => '1',
                    'title' => 'Test Push Notification',
                    'body' => 'This is a test push notification',
                    'data' => ['key' => 'value'],
                    'user_ids' => ['1', '2', '3'],
                    'status_name' => 'Pending',
                    'push_notification_status_id' => '1',
                    'total_devices' => 100,
                    'success_count' => 0,
                    'failure_count' => 0,
                    'created_at' => '2021-01-01 00:00:00',
                    'updated_at' => '2021-01-01 00:00:00',
                    'histories' => [
                        [
                            'id' => 1,
                            'push_notification_id' => 1,
                            'push_notification_status_id' => 1,
                            'created_at' => '2021-01-01 00:00:00',
                            'updated_at' => '2021-01-01 00:00:00',
                        ],
                    ],
                ],
            ];
        }

        return [
            'status' => 'OK',
            'message' => null,
            'data' => [
                'id' => 1,
                'channel_provider_id' => 1,
                'message_status_id' => 1,
                'created_user_id' => '1',
                'attempts' => 1,
                'updated_at' => '2021-01-01 00:00:00',
                'created_at' => '2021-01-01 00:00:00',
                'message_histories' => [
                    [
                        'id' => 1,
                        'message_id' => 1,
                        'message_status_id' => 1,
                        'created_at' => '2021-01-01 00:00:00',
                        'updated_at' => '2021-01-01 00:00:00',
                    ],
                ],
            ],
        ];
    }

    protected function fakeStatusCode(): int
    {
        if ($this->currentUrl && str_contains($this->currentUrl, '/api/v1/push-notifications/') && $this->currentMethod === 'DELETE') {
            return 204;
        }

        return 200;
    }
}
