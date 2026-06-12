<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Channels;

use AndyDefer\LaravelFeatures\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Notifications\Exceptions\NotificationSendException;
use AndyDefer\LaravelFeatures\Notifications\Models\Notification;

abstract class AbstractChannel implements ChannelInterface
{
    public function send(NotifiableInterface $notifiable, Notification $notification): bool
    {
        $this->before($notifiable, $notification);

        try {
            $result = $this->execute($notifiable, $notification);
            $this->after($notifiable, $notification, $result, null);

            return $result;
        } catch (\Exception $e) {
            $this->after($notifiable, $notification, false, $e);
            throw new NotificationSendException($e->getMessage());
        }
    }

    protected function before(NotifiableInterface $notifiable, Notification $notification): void
    {
        // À surcharger si besoin
    }

    protected function after(
        NotifiableInterface $notifiable,
        Notification $notification,
        bool $success,
        ?\Exception $error = null
    ): void {
        // À surcharger si besoin
    }

    abstract protected function execute(NotifiableInterface $notifiable, Notification $notification): bool;
}
