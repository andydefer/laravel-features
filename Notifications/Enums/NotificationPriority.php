<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Enums;

enum NotificationPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Basse',
            self::NORMAL => 'Normale',
            self::HIGH => 'Haute',
            self::URGENT => 'Urgente',
        };
    }
}
