<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Notifications\Models;

use AndyDefer\LaravelFeatures\Notifications\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Model;

final class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'channel',
        'notifiable_type',
        'notifiable_id',
        'data',
        'status',
        'error',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
    ];

    public function markAsSent(): void
    {
        $this->update([
            'status' => NotificationStatus::SENT->value,
            'sent_at' => now(),
            'error' => null,
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => NotificationStatus::FAILED->value,
            'error' => $error,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === NotificationStatus::PENDING->value;
    }

    public function isSent(): bool
    {
        return $this->status === NotificationStatus::SENT->value;
    }

    public function isFailed(): bool
    {
        return $this->status === NotificationStatus::FAILED->value;
    }

    public function getNotifiable(): ?Model
    {
        return $this->notifiable_type::find($this->notifiable_id);
    }
}
