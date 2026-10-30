<?php

namespace AxoloteSource\MessagesSdk\DTO;

class Provider
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly bool $active,
        public readonly array $channels,
        public readonly ?string $driverName,
        public readonly bool $default,
        public readonly ?ProviderConfig $providerConfig = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $item = $data['data'] ?? $data;

        return new self(
            id: $item['id'],
            name: $item['name'] ?? '',
            active: (bool) ($item['active'] ?? true),
            channels: $item['channels'] ?? [],
            driverName: $item['driver_name'] ?? null,
            default: (bool) ($item['default'] ?? false),
            providerConfig: isset($item['provider_config']) && is_array($item['provider_config'])
                ? ProviderConfig::fromArray($item['provider_config'])
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'channels' => $this->channels,
            'driver_name' => $this->driverName,
            'default' => $this->default,
            'provider_config' => $this->providerConfig?->toArray(),
        ];
    }
}
