<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;

final class NotificationRecord extends AbstractRecord
{
    public function __construct(
        public readonly string $type,
        public readonly string $channel,
        public readonly string $notifiable_type,
        public readonly int $notifiable_id,
        public readonly array $data,
        public readonly string $status = 'pending',
        public readonly ?string $error = null,
        public readonly ?string $sent_at = null,
    ) {}
}
