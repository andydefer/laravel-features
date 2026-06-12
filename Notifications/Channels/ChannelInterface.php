<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Channels;

use AndyDefer\LaravelFeatures\Notifications\Contracts\NotifiableInterface;
use AndyDefer\LaravelFeatures\Notifications\Models\Notification;

interface ChannelInterface
{
    public function send(NotifiableInterface $notifiable, Notification $notification): bool;
}
