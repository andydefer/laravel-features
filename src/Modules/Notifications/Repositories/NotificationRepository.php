<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Repositories;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationStatus;
use AndyDefer\LaravelFeatures\Modules\Notifications\Models\Notification;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationFilterRecord;
use AndyDefer\LaravelFeatures\Modules\Notifications\Records\NotificationRecord;
use AndyDefer\Repository\AbstractRepository;
use AndyDefer\Repository\Records\FindByRecord;
use AndyDefer\Repository\ValueObjects\SortColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class NotificationRepository extends AbstractRepository
{
    private HydrationService $hydration;

    public function __construct(HydrationService $hydration)
    {
        parent::__construct(
            modelClass: Notification::class,
            recordClass: NotificationRecord::class,
        );
        $this->hydration = $hydration;
    }

    public function find(int $id): ?Notification
    {
        return parent::find($id);
    }

    public function markAsSent(Notification $notification): void
    {
        $this->updateRaw($notification->id, [
            'status' => NotificationStatus::SENT->value,
            'sent_at' => now()->toDateTimeString(),
            'error' => null,
        ]);
    }

    public function markAsFailed(Notification $notification, string $error): void
    {
        $this->updateRaw($notification->id, [
            'status' => NotificationStatus::FAILED->value,
            'error' => $error,
        ]);
    }

    public function findPending(): Collection
    {
        $filter = $this->hydration->hydrate(NotificationFilterRecord::class, [
            'status' => NotificationStatus::PENDING->value,
        ]);

        $findByRecord = new FindByRecord(
            filters: $filter,
            sortBy: new SortColumns('created_at:asc'),
        );

        return $this->findBy($findByRecord);
    }

    public function findByNotifiable(Model $notifiable): Collection
    {
        $filter = $this->hydration->hydrate(NotificationFilterRecord::class, [
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
        ]);

        $findByRecord = new FindByRecord(
            filters: $filter,
            sortBy: new SortColumns('created_at:desc'),
        );

        return $this->findBy($findByRecord);
    }

    protected function applyFilters(Builder $query, AbstractRecord $filters): void
    {
        if (! $filters instanceof NotificationFilterRecord) {
            return;
        }

        if ($filters->id !== null) {
            $query->where('id', $filters->id);
        }

        if ($filters->type !== null) {
            $query->where('type', $filters->type);
        }

        if ($filters->channel !== null) {
            $query->where('channel', $filters->channel);
        }

        if ($filters->notifiable_type !== null) {
            $query->where('notifiable_type', $filters->notifiable_type);
        }

        if ($filters->notifiable_id !== null) {
            $query->where('notifiable_id', $filters->notifiable_id);
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status);
        }

        if ($filters->created_before !== null) {
            $query->where('created_at', '<', $filters->created_before);
        }

        if ($filters->created_after !== null) {
            $query->where('created_at', '>', $filters->created_after);
        }
    }
}
