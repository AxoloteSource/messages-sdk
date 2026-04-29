<?php

namespace AxoloteSource\MessagesSdk\DTO;

class Message
{
    public function __construct(
        public readonly int $id,
        public readonly int $channel_provider_id,
        public readonly int $message_status_id,
        public readonly string $created_user_id,
        public readonly int $attempts,
        public readonly string $updatedAt,
        public readonly string $createdAt,
        public readonly array $message_histories,
    ) {}

    public static function fromArray(array $data): self
    {
        $messageData = $data['data'] ?? [];
        $messageHistories = $data['data']['message_histories'] ?? [];

        return new self(
            id: $messageData['id'],
            channel_provider_id: $messageData['channel_provider_id'],
            message_status_id: $messageData['message_status_id'],
            created_user_id: $messageData['created_user_id'],
            attempts: $messageData['attempts'],
            updatedAt: $messageData['updated_at'],
            createdAt: $messageData['created_at'],
            message_histories: $messageHistories
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'channel_provider_id' => $this->channel_provider_id,
            'message_status_id' => $this->message_status_id,
            'created_user_id' => $this->created_user_id,
            'attempts' => $this->attempts,
            'updated_at' => $this->updatedAt,
            'created_at' => $this->createdAt,
            'message_histories' => $this->message_histories,
        ];
    }
}
