<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Comments\Models;

use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Comments\Enums\CommentStatus;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Comment extends Model
{
    use SoftDeletes;

    protected $table = 'comments';

    protected $fillable = [
        'commenter_type',
        'commenter_id',
        'commentable_type',
        'commentable_id',
        'content',
        'parent_id',
        'status',
        'metadata',
    ];

    protected $casts = [
        'status' => CommentStatus::class,
        'metadata' => 'array',
    ];

    public function commenter()
    {
        return $this->morphTo();
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
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

    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? StrictDataObject::from($value) : null,
        );
    }
}
