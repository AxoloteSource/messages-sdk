<?php

namespace AxoloteSource\MessagesSdk\DTO;

class IncomingMessage
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $contactId,
        public readonly string $waMessageId,
        public readonly string $type,
        public readonly ?string $text,
        public readonly ?array $media,
        public readonly ?array $raw,
        public readonly ?int $waTimestamp,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $item = $data['data'] ?? $data;

        return new self(
            id: $item['id'],
            contactId: $item['contact_id'] ?? $item['whatsapp_contact_id'],
            waMessageId: $item['wa_message_id'],
            type: $item['type'] ?? 'unknown',
            text: $item['text'] ?? null,
            media: $item['media'] ?? null,
            raw: $item['raw'] ?? null,
            waTimestamp: isset($item['wa_timestamp']) ? (int) $item['wa_timestamp'] : null,
            createdAt: $item['created_at'] ?? null,
            updatedAt: $item['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'contact_id' => $this->contactId,
            'wa_message_id' => $this->waMessageId,
            'type' => $this->type,
            'text' => $this->text,
            'media' => $this->media,
            'raw' => $this->raw,
            'wa_timestamp' => $this->waTimestamp,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
