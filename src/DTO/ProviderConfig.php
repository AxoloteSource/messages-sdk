<?php

namespace AxoloteSource\MessagesSdk\DTO;

class ProviderConfig
{
    public function __construct(
        public readonly int|string $id,
        public readonly string $name,
        public readonly int $driverId,
        public readonly ?string $driverName,
        public readonly bool $active,
        public readonly bool $default,
        public readonly array $config,
    ) {}

    public static function fromArray(array $data): self
    {
        $item = $data['data'] ?? $data;

        return new self(
            id: $item['id'],
            name: $item['name'] ?? '',
            driverId: (int) ($item['driver_id'] ?? 0),
            driverName: $item['driver_name'] ?? null,
            active: (bool) ($item['active'] ?? true),
            default: (bool) ($item['default'] ?? false),
            config: $item['config'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'driver_id' => $this->driverId,
            'driver_name' => $this->driverName,
            'active' => $this->active,
            'default' => $this->default,
            'config' => $this->config,
        ];
    }
}
