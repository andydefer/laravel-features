<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Enums;

use AndyDefer\LaravelFeatures\Notifications\Channels\ChannelInterface;
use AndyDefer\LaravelFeatures\Notifications\Channels\DatabaseChannel;
use AndyDefer\LaravelFeatures\Notifications\Channels\MailChannel;

enum NotificationChannel: string
{
    case MAIL = 'mail';
    case DATABASE = 'database';

    public function getChannel(): ChannelInterface
    {
        return match ($this) {
            self::MAIL => new MailChannel,
            self::DATABASE => new DatabaseChannel,
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
