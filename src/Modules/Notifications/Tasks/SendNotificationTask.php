<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Tasks;

use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationRecord;
use AndyDefer\LaravelFeatures\Modules\Notifications\Services\NotificationService;
use AndyDefer\Task\AbstractTask;
use AndyDefer\Task\Records\TaskConfigRecord;

final class SendNotificationTask extends AbstractTask
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly HydrationService $hydration,
    ) {}

    public function getConfig(): TaskConfigRecord
    {
        return new TaskConfigRecord(
            signature: 'send-notification',
            description: 'Send a notification asynchronously',
            delaySeconds: 0,
            maxAttempts: 3,
        );
    }

    protected function process(): void
    {
        $data = $this->payload->data->first();

        $recordData = $data->notification_record ?? null;

        if (! $recordData) {
            $this->error('Notification record not found in payload');

            return;
        }

        // Hydratation directe
        $record = $this->hydration->hydrate(NotificationRecord::class, $recordData);

        $this->notificationService->sendNow($record);

        $this->info('Notification sent successfully');
    }
}
