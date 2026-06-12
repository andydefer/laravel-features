<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Exceptions;

use RuntimeException;

final class NotificationSendException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
