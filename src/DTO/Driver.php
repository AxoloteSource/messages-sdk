<?php

namespace AxoloteSource\MessagesSdk\DTO;

class Driver
{
    public function __construct(
        public readonly int|string $id,
        public readonly string $name,
        public readonly string $configKey,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $item = $data['data'] ?? $data;

        return new self(
            id: $item['id'],
            name: $item['name'],
            configKey: $item['config_key'] ?? $item['configKey'] ?? '',
            createdAt: $item['created_at'] ?? null,
            updatedAt: $item['updated_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'config_key' => $this->configKey,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
