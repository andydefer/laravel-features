<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Exceptions;

use RuntimeException;

final class ChannelNotFoundException extends RuntimeException
{
    public function __construct(string $channel)
    {
        parent::__construct(sprintf('Channel "%s" not found', $channel));
    }
}
