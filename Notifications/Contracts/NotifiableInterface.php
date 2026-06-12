<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Contracts;

interface NotifiableInterface
{
    public function getNotificationDestination(): string;

    public function getMorphClass(): string;

    public function getKey(): int;
}
