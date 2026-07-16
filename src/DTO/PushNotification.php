<?php

namespace AxoloteSource\MessagesSdk\DTO;

class PushNotification
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $body,
        public readonly ?array $data,
        public readonly ?array $userIds,
        public readonly ?string $statusName,
        public readonly string $pushNotificationStatusId,
        public readonly int $totalDevices,
        public readonly int $successCount,
        public readonly int $failureCount,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly array $histories,
    ) {}

    public static function fromArray(array $data): self
    {
        $item = $data['data'] ?? $data;

        return new self(
            id: $item['id'],
            title: $item['title'],
            body: $item['body'],
            data: $item['data'] ?? null,
            userIds: $item['user_ids'] ?? null,
            statusName: $item['status_name'] ?? null,
            pushNotificationStatusId: $item['push_notification_status_id'],
            totalDevices: $item['total_devices'] ?? 0,
            successCount: $item['success_count'] ?? 0,
            failureCount: $item['failure_count'] ?? 0,
            createdAt: $item['created_at'],
            updatedAt: $item['updated_at'],
            histories: $item['histories'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
            'user_ids' => $this->userIds,
            'status_name' => $this->statusName,
            'push_notification_status_id' => $this->pushNotificationStatusId,
            'total_devices' => $this->totalDevices,
            'success_count' => $this->successCount,
            'failure_count' => $this->failureCount,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'histories' => $this->histories,
        ];
    }
}
