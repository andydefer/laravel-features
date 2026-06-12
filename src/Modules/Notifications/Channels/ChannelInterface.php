<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Channels;

use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationRecord;

interface ChannelInterface
{
    public function send(NotifiableInterface $notifiable, NotificationRecord $record): bool;
}
