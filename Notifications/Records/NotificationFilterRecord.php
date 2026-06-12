<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;

final class NotificationFilterRecord extends AbstractRecord
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $type = null,
        public readonly ?string $channel = null,
        public readonly ?string $notifiable_type = null,
        public readonly ?int $notifiable_id = null,
        public readonly ?string $status = null,
        public readonly ?string $created_before = null,
        public readonly ?string $created_after = null,
    ) {}
}
