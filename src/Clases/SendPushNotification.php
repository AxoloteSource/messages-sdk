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

    protected function request(string $method, ?string $url = null, ?array $data = null): PromiseInterface|Response
    {
        $this->currentUrl = $url;

        return parent::request($method, $url, $data);
    }

    protected function fakeResponse(): array
    {
        if ($this->currentUrl && str_contains($this->currentUrl, '/api/v1/push-notifications/')) {
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
}
