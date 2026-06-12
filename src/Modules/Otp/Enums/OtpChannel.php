<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Otps\Enums;

enum OtpChannel: string
{
    case MAIL = 'mail';
    case SMS = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::MAIL => 'Email',
            self::SMS => 'SMS',
        };
    }
}
