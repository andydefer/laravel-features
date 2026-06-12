<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Services;

use AndyDefer\LaravelFeatures\Modules\Notifications\Contracts\DestinationValidatorInterface;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationDestinationRecord;

final class DestinationValidatorService implements DestinationValidatorInterface
{
    public function validate(NotificationDestinationRecord $record): void
    {
        match ($record->channel) {
            NotificationChannel::MAIL => $this->validateEmail($record->value),
            NotificationChannel::DATABASE => $this->validateDatabase($record->value),
        };
    }

    private function validateEmail(string $value): void
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email address: {$value}");
        }
    }

    private function validateDatabase(string $value): void
    {
        if (! is_numeric($value)) {
            throw new \InvalidArgumentException("Invalid database identifier: {$value}");
        }
    }
}
