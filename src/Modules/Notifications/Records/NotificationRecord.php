<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationStatus;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;

final class NotificationRecord extends AbstractRecord
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $type = null,
        public readonly ?string $channel = null,
        public readonly ?string $notifiable_type = null,
        public readonly ?int $notifiable_id = null,
        public readonly StrictDataObject $data = new StrictDataObject([]),
        public readonly NotificationStatus $status = NotificationStatus::PENDING,
        public readonly ?string $error = null,
        public readonly ?DateTimeVO $sent_at = null,
    ) {}
}
