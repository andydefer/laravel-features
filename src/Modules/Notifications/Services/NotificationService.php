<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Services;

use AndyDefer\DomainStructures\Collections\Utility\StrictDataObjectCollection;
use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationStatus;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationRecord;
use AndyDefer\LaravelFeatures\Modules\Notifications\Repositories\NotificationRepository;
use AndyDefer\LaravelFeatures\Modules\Notifications\Tasks\SendNotificationTask;
use AndyDefer\Logger\Contracts\LoggerInterface;
use AndyDefer\Logger\Records\LogDataRecord;
use AndyDefer\Task\Records\TaskPayloadRecord;
use AndyDefer\Task\Services\TaskRegistryService;
use Illuminate\Contracts\Container\Container;

final class NotificationService
{
    public function __construct(
        private readonly TaskRegistryService $taskRegistry,
        private readonly NotificationRepository $repository,
        private readonly HydrationService $hydration,
        private readonly LoggerInterface $logger,
        private readonly Container $container,
    ) {}

    public function send(NotifiableInterface $notifiable, array $data, NotificationChannel $channel): void
    {
        $destinations = $notifiable->getNotificationDestinations()->getByChannel($channel);

        if ($destinations->count() === 0) {
            throw new \RuntimeException("No destination found for channel: {$channel->value}");
        }

        foreach ($destinations as $destination) {
            $record = $this->prepareRecord($notifiable, $data, $channel, $destination->value);
            $this->sendNow($record);
        }
    }

    public function sendAsync(NotifiableInterface $notifiable, array $data, NotificationChannel $channel, int $delaySeconds = 0): array
    {
        $destinations = $notifiable->getNotificationDestinations()->getByChannel($channel);

        if ($destinations->count() === 0) {
            throw new \RuntimeException("No destination found for channel: {$channel->value}");
        }

        $taskIds = [];

        foreach ($destinations as $destination) {
            $record = $this->prepareRecord($notifiable, $data, $channel, $destination->value);

            $recordData = $record->toArray();

            $payload = $this->hydration->hydrate(TaskPayloadRecord::class, [
                'type' => 'send_notification',
                'data' => $this->hydration->hydrate(StrictDataObjectCollection::class, [
                    $this->hydration->hydrate(StrictDataObject::class, [
                        'notification_record' => $recordData,
                    ]),
                ]),
            ]);

            $taskId = $this->taskRegistry->register(
                taskClass: SendNotificationTask::class,
                payload: $payload,
                delaySeconds: $delaySeconds,
            );

            $taskIds[] = $taskId;
        }

        $logPayload = new StrictDataObject([
            'event' => 'notification_dispatched_async',
            'channel' => $channel->value,
            'task_ids' => $taskIds,
            'delay_seconds' => $delaySeconds,
        ]);

        $this->logger->info(new LogDataRecord(
            type: 'notification',
            payload: $logPayload
        ));

        return $taskIds;
    }

    public function sendBulk(array $notifiables, array $data, NotificationChannel $channel, int $delaySeconds = 0): array
    {
        $allTaskIds = [];

        foreach ($notifiables as $notifiable) {
            $taskIds = $this->sendAsync($notifiable, $data, $channel, $delaySeconds);
            $allTaskIds = array_merge($allTaskIds, $taskIds);
        }

        $logPayload = new StrictDataObject([
            'event' => 'notification_bulk_dispatched',
            'channel' => $channel->value,
            'count' => count($allTaskIds),
            'delay_seconds' => $delaySeconds,
        ]);

        $this->logger->info(new LogDataRecord(
            type: 'notification',
            payload: $logPayload
        ));

        return $allTaskIds;
    }

    public function sendNow(NotificationRecord $record): void
    {
        $channel = NotificationChannel::from($record->channel);
        $channelHandler = $this->container->make($channel->getChannelClassName());
        $notification = $this->repository->find($record->id);

        if (! $notification) {
            return;
        }

        $notifiable = $notification->getNotifiable();

        if (! $notifiable instanceof NotifiableInterface) {
            $this->repository->markAsFailed($notification, 'Notifiable does not implement NotifiableInterface');

            $logPayload = new StrictDataObject([
                'event' => 'notification_failed',
                'error' => 'Notifiable does not implement NotifiableInterface',
            ]);

            $this->logger->error(new LogDataRecord(
                type: 'notification',
                payload: $logPayload
            ));

            return;
        }

        try {
            $channelHandler->send($notifiable, $record);
            $this->repository->markAsSent($notification);

            $logPayload = new StrictDataObject([
                'event' => 'notification_sent',
                'channel' => $record->channel,
                'type' => $record->type,
            ]);

            $this->logger->info(new LogDataRecord(
                type: 'notification',
                payload: $logPayload
            ));
        } catch (\Exception $e) {
            $this->repository->markAsFailed($notification, $e->getMessage());

            $logPayload = new StrictDataObject([
                'event' => 'notification_failed',
                'channel' => $record->channel,
                'error' => $e->getMessage(),
            ]);

            $this->logger->error(new LogDataRecord(
                type: 'notification',
                payload: $logPayload
            ));

            throw $e;
        }
    }

    private function prepareRecord(NotifiableInterface $notifiable, array $data, NotificationChannel $channel, string $destination): NotificationRecord
    {
        return $this->hydration->hydrate(NotificationRecord::class, [
            'type' => $data['type'] ?? 'generic',
            'channel' => $channel->value,
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
            'destination' => $destination,
            'data' => new StrictDataObject($data),
            'status' => NotificationStatus::PENDING,
        ]);
    }
}
