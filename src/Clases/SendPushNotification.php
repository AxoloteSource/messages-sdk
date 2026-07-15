<?php

namespace AxoloteSource\MessagesSdk\Clases;

use AxoloteSource\MessagesSdk\DTO\Message;

class SendPushNotification extends AxMessagesBase
{
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

    protected function fakeResponse(): array
    {
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
