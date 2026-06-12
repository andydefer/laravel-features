<?php

// src/Records/OtpSecurityConfigRecord.php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Otps\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;

final class OtpSecurityConfigRecord extends AbstractRecord
{
    public function __construct(
        public readonly int $rate_limit_requests,
        public readonly int $rate_limit_verifications,
        public readonly int $rate_limit_decay_minutes,
        public readonly int $failed_verification_decay_seconds,
        public readonly int $rate_limit_hit_decay_seconds,
    ) {}
}
