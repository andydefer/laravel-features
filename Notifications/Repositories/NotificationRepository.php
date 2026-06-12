<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Repositories;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\LaravelFeatures\Notifications\Models\Notification;
use AndyDefer\LaravelFeatures\Notifications\Records\NotificationFilterRecord;
use AndyDefer\LaravelFeatures\Notifications\Records\NotificationRecord;
use AndyDefer\Repository\AbstractRepository;
use AndyDefer\Repository\Records\FindByRecord;
use AndyDefer\Repository\ValueObjects\SortColumns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class NotificationRepository extends AbstractRepository
{
    private HydrationService $hydration;

    public function __construct()
    {
        parent::__construct(
            modelClass: Notification::class,
            recordClass: NotificationRecord::class,
        );
        $this->hydration = new HydrationService;
    }

    public function findPending(): Collection
    {
        $filter = $this->hydration->hydrate(NotificationFilterRecord::class, [
            'status' => 'pending',
        ]);

        $findByRecord = new FindByRecord(
            filters: $filter,
            sortBy: new SortColumns('created_at:asc'),
        );

        return $this->findBy($findByRecord);
    }

    public function markAsSent(int $id): void
    {
        $notification = $this->find($id);
        if ($notification) {
            $notification->markAsSent();
        }
    }

    public function markAsFailed(int $id, string $error): void
    {
        $notification = $this->find($id);
        if ($notification) {
            $notification->markAsFailed($error);
        }
    }

    public function findByNotifiable(string $notifiableType, int $notifiableId): Collection
    {
        $filter = $this->hydration->hydrate(NotificationFilterRecord::class, [
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiableId,
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
