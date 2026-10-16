<?php

namespace AxoloteSource\MessagesSdk\DTO;

class ReplicateUrl
{
    public function __construct(
        public readonly int|string $id,
        public readonly string $url,
        public readonly bool $isActive,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $item = $data['data'] ?? $data;

        return new self(
            id: $item['id'],
            url: $item['url'],
            isActive: (bool) ($item['is_active'] ?? true),
            createdAt: $item['created_at'] ?? null,
            updatedAt: $item['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
