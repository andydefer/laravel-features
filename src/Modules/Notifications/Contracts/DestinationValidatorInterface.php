<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Contracts;

use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationDestinationRecord;

interface DestinationValidatorInterface
{
    public function validate(NotificationDestinationRecord $record): void;
}
