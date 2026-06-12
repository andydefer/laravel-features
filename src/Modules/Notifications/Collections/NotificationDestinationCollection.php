<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Collections;

use AndyDefer\DomainStructures\Abstracts\AbstractTypedCollection;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationDestinationRecord;

final class NotificationDestinationCollection extends AbstractTypedCollection
{
    public function __construct()
    {
        parent::__construct(NotificationDestinationRecord::class);
    }

    public function getByChannel(NotificationChannel $channel): self
    {
        $collection = new self;

        foreach ($this->items as $item) {
            if ($item->channel === $channel) {
                $collection->add($item);
            }
        }

        return $collection;
    }

    public function hasChannel(NotificationChannel $channel): bool
    {
        return $this->getByChannel($channel)->count() > 0;
    }
}
