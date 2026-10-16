<?php

namespace AxoloteSource\MessagesSdk\DTO;

class Contact
{
    public function __construct(
        public readonly int|string $id,
        public readonly string $phone,
        public readonly ?string $name,
        public readonly ?string $lastMessageText,
        public readonly ?string $lastMessageAt,
        public readonly int $unreadCount,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?int $messagesCount = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $item = $data['data'] ?? $data;

        return new self(
            id: $item['id'],
            phone: $item['phone'],
            name: $item['name'] ?? null,
            lastMessageText: $item['last_message_text'] ?? null,
            lastMessageAt: $item['last_message_at'] ?? null,
            unreadCount: (int) ($item['unread_count'] ?? 0),
            createdAt: $item['created_at'] ?? null,
            updatedAt: $item['updated_at'] ?? null,
            messagesCount: isset($item['messages_count']) ? (int) $item['messages_count'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'name' => $this->name,
            'last_message_text' => $this->lastMessageText,
            'last_message_at' => $this->lastMessageAt,
            'unread_count' => $this->unreadCount,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'messages_count' => $this->messagesCount,
        ];
    }
}
