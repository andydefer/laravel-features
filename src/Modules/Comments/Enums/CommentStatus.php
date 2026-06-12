<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Comments\Enums;

enum CommentStatus: string
{
    case PUBLISHED = 'published';
    case HIDDEN = 'hidden';
    case FLAGGED = 'flagged';

    public function getLabel(): string
    {
        return match ($this) {
            self::PUBLISHED => 'Publié',
            self::HIDDEN => 'Masqué',
            self::FLAGGED => 'Signalé',
        };
    }

    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    public function isHidden(): bool
    {
        return $this === self::HIDDEN;
    }

    public function isFlagged(): bool
    {
        return $this === self::FLAGGED;
    }
}
