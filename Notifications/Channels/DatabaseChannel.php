<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Channels;

use AndyDefer\LaravelFeatures\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Notifications\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class DatabaseChannel extends AbstractChannel
{
    protected function before(NotifiableInterface $notifiable, Notification $notification): void
    {
        Log::info("Storing notification for user {$notifiable->getKey()}");
    }

    protected function execute(NotifiableInterface $notifiable, Notification $notification): bool
    {
        DB::table('user_notifications')->insert([
            'user_id' => $notifiable->getKey(),
            'notification_id' => $notification->id,
            'type' => $notification->type,
            'data' => json_encode($notification->data),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    protected function after(
        NotifiableInterface $notifiable,
        Notification $notification,
        bool $success,
        ?\Exception $error = null
    ): void {
        if ($success) {
            Log::info("Notification stored for user {$notifiable->getKey()}");
        } else {
            Log::error("Failed to store notification for user {$notifiable->getKey()}: {$error->getMessage()}");
        }
    }
}
