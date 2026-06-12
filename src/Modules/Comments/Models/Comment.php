<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Comments\Models;

use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Modules\Comments\Enums\CommentStatus;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
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

    public function getCreatedAt(): ?DateTimeVO
    {
        $value = $this->created_at;

        return $value ? new DateTimeVO($value) : null;
    }

    public function getUpdatedAt(): ?DateTimeVO
    {
        $value = $this->updated_at;

        return $value ? new DateTimeVO($value) : null;
    }

    public function getMetadata(): ?StrictDataObject
    {
        $value = $this->metadata;

        if ($value === null) {
            return null;
        }

        return is_array($value) ? new StrictDataObject($value) : null;
    }
}
