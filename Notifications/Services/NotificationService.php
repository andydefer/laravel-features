<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Services;

use AndyDefer\DomainStructures\Collections\Utility\StrictDataObjectCollection;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Notifications\Enums\NotificationChannel;
use AndyDefer\LaravelFeatures\Notifications\Models\Notification;
use AndyDefer\LaravelFeatures\Notifications\Records\NotificationRecord;
use AndyDefer\LaravelFeatures\Notifications\Repositories\NotificationRepository;
use AndyDefer\LaravelFeatures\Notifications\Tasks\SendNotificationTask;
use AndyDefer\Task\Records\TaskPayloadRecord;
use AndyDefer\Task\Services\TaskRegistryService;

final class NotificationService
{
    public function __construct(
        private readonly TaskRegistryService $taskRegistry,
        private readonly NotificationRepository $repository,
    ) {}

    public function send(NotifiableInterface $notifiable, array $data, NotificationChannel $channel): Notification
    {
        $notification = $this->createNotification($notifiable, $data, $channel);
        $this->sendNow($notification);

        return $notification;
    }

    public function sendAsync(NotifiableInterface $notifiable, array $data, NotificationChannel $channel, int $delaySeconds = 0): string
    {
        $notification = $this->createNotification($notifiable, $data, $channel);

        $payload = new TaskPayloadRecord(
            type: 'send_notification',
            data: StrictDataObjectCollection::from([
                StrictDataObject::from(['notification_id' => $notification->id]),
            ]),
        );

        return $this->taskRegistry->register(
            taskClass: SendNotificationTask::class,
            payload: $payload,
            delaySeconds: $delaySeconds,
        );
    }

    public function sendBulk(array $notifiables, array $data, NotificationChannel $channel, int $delaySeconds = 0): array
    {
        $taskIds = [];

        foreach ($notifiables as $notifiable) {
            $taskIds[] = $this->sendAsync($notifiable, $data, $channel, $delaySeconds);
        }

        return $taskIds;
    }

    public function sendNow(Notification $notification): void
    {
        $channel = NotificationChannel::from($notification->channel)->getChannel();
        $notifiable = $notification->getNotifiable();

        if (! $notifiable instanceof NotifiableInterface) {
            $notification->markAsFailed('Notifiable does not implement NotifiableInterface');

            return;
        }

        try {
            $channel->send($notifiable, $notification);
            $notification->markAsSent();
        } catch (\Exception $e) {
            $notification->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    private function createNotification(NotifiableInterface $notifiable, array $data, NotificationChannel $channel): Notification
    {
        $record = new NotificationRecord(
            type: $data['type'] ?? 'generic',
            channel: $channel->value,
            notifiable_type: $notifiable->getMorphClass(),
            notifiable_id: $notifiable->getKey(),
            data: $data,
        );

        return $this->repository->create($record);
    }
}
