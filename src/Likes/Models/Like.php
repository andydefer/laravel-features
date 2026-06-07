<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Likes\Models;

use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Likes\Enums\LikeType;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Like extends Model
{
    use SoftDeletes;

    protected $table = 'likes';

    protected $fillable = [
        'liker_type',
        'liker_id',
        'likeable_type',
        'likeable_id',
        'type',
        'metadata',
    ];

    protected $casts = [
        'type' => LikeType::class,
        'metadata' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function liker()
    {
        return $this->morphTo();
    }

    public function likeable()
    {
        return $this->morphTo();
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? DateTimeVO::from($value) : null,
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? DateTimeVO::from($value) : null,
        );
    }

    protected function deletedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? DateTimeVO::from($value) : null,
        );
    }

    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? StrictDataObject::from($value) : null,
        );
    }
}
