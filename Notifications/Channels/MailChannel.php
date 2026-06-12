<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Channels;

use AndyDefer\LaravelFeatures\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Notifications\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class MailChannel extends AbstractChannel
{
    protected function before(NotifiableInterface $notifiable, Notification $notification): void
    {
        $destination = $notifiable->getNotificationDestination();

        if (! filter_var($destination, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email address: {$destination}");
        }

        Log::info("Preparing mail for {$destination}");
    }

    protected function execute(NotifiableInterface $notifiable, Notification $notification): bool
    {
        $data = $notification->data;
        $destination = $notifiable->getNotificationDestination();

        Mail::send([], [], function ($message) use ($destination, $data) {
            $message->to($destination)
                ->subject($data['subject'] ?? 'Notification')
                ->html($data['body'] ?? '');
        });

        return true;
    }

    protected function after(
        NotifiableInterface $notifiable,
        Notification $notification,
        bool $success,
        ?\Exception $error = null
    ): void {
        $destination = $notifiable->getNotificationDestination();

        if ($success) {
            Log::info("Mail sent successfully to {$destination}");
        } else {
            Log::error("Mail failed to {$destination}: {$error->getMessage()}");
        }
    }
}
