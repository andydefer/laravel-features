<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Enums;

use AndyDefer\LaravelFeatures\Modules\Notifications\Channels\DatabaseChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Channels\MailChannel;

enum NotificationChannel: string
{
    case MAIL = 'mail';
    case DATABASE = 'database';

    public function getChannelClassName(): string
    {
        return match ($this) {
            self::MAIL => MailChannel::class,
            self::DATABASE => DatabaseChannel::class,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::MAIL => 'Email',
            self::DATABASE => 'Base de données',
        };
    }
}
