<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Contracts;

interface NotificationInterface
{
    public function getType(): string;

    public function getData(): array;

    public function getChannel(): string;
}
