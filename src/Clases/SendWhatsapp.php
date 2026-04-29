<?php

namespace AxoloteSource\MessagesSdk\Clases;

use AxoloteSource\MessagesSdk\DTO\Message;

class SendWhatsapp extends AxMessagesBase
{
    public function __construct()
    {
        parent::__construct('/api/v1/messages/whatsapp/send');
    }

    public function template(string $to, string $templateName, array $variables = []): ?Message
    {
        try {
            $response = $this->post([
                'template_name' => $templateName,
                'phone_number' => $to,
                'params' => $variables,
            ]);

            if ($response->successful()) {
                return Message::fromArray($response->json());
            }

            if ($this->debugMode) {
                logger()->error('Error en el envió de Whatsapp', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            logger()->error('Excepción en el envió de Whatsapp', ['exception' => $e]);
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
