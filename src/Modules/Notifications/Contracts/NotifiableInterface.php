<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Contracts;

use AndyDefer\LaravelFeatures\Modules\Notifications\Collections\NotificationDestinationCollection;

interface NotifiableInterface
{
    public function getNotificationDestinations(): NotificationDestinationCollection;

    public function getMorphClass(): string;

    public function getKey(): int;
}
