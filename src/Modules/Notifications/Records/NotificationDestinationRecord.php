<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationChannel;

final class NotificationDestinationRecord extends AbstractRecord
{
    public function __construct(
        public readonly NotificationChannel $channel,
        public readonly string $value,
    ) {}
}
