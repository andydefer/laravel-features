<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Channels;

use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Exceptions\NotificationSendException;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationRecord;

abstract class AbstractChannel implements ChannelInterface
{
    public function send(NotifiableInterface $notifiable, NotificationRecord $record): bool
    {
        $this->before($notifiable, $record);

        try {
            $result = $this->execute($notifiable, $record);
            $this->after($notifiable, $record, $result, null);

            return $result;
        } catch (\Exception $e) {
            $this->after($notifiable, $record, false, $e);
            throw new NotificationSendException($e->getMessage());
        }
    }

    protected function before(NotifiableInterface $notifiable, NotificationRecord $record): void
    {
        // À surcharger si besoin
    }

    protected function after(
        NotifiableInterface $notifiable,
        NotificationRecord $record,
        bool $success,
        ?\Exception $error = null
    ): void {
        // À surcharger si besoin
    }

    abstract protected function execute(NotifiableInterface $notifiable, NotificationRecord $record): bool;
}
