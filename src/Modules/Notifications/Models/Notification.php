<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Notifications\Models;

use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationChannel;
use AndyDefer\LaravelFeatures\Modules\Notifications\Enums\NotificationStatus;
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
        'status' => NotificationStatus::class,
        'channel' => NotificationChannel::class,
        'sent_at' => 'datetime',
    ];

    public function isPending(): bool
    {
        return $this->status === NotificationStatus::PENDING;
    }

    public function isSent(): bool
    {
        return $this->status === NotificationStatus::SENT;
    }

    public function isFailed(): bool
    {
        return $this->status === NotificationStatus::FAILED;
    }

    public function getNotifiable(): ?Model
    {
        return $this->notifiable_type::find($this->notifiable_id);
    }
}
