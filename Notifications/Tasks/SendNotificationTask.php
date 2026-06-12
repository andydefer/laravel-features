<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Tasks;

use AndyDefer\LaravelFeatures\Notifications\Repositories\NotificationRepository;
use AndyDefer\LaravelFeatures\Notifications\Services\NotificationService;
use AndyDefer\Task\AbstractTask;
use AndyDefer\Task\Records\TaskConfigRecord;

final class SendNotificationTask extends AbstractTask
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly NotificationRepository $repository,
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
        $data = $this->payload->payload->first();
        $notificationId = $data->notification_id ?? null;

        if (! $notificationId) {
            $this->error('Notification ID not found in payload');

            return;
        }

        $notification = $this->repository->find($notificationId);

        if (! $notification) {
            $this->error("Notification {$notificationId} not found");

            return;
        }

        $this->notificationService->sendNow($notification);

        $this->info("Notification {$notificationId} sent successfully");
    }
}
