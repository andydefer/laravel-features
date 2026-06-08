<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Ratings\Models;

use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Ratings\Enums\RatingLevel;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Rating extends Model
{
    use SoftDeletes;

    protected $table = 'ratings';

    protected $fillable = [
        'rater_type',
        'rater_id',
        'rateable_type',
        'rateable_id',
        'rating_level',
        'review',
        'metadata',
    ];

    protected $casts = [
        'rating_level' => RatingLevel::class,
        'metadata' => 'array',
    ];

    public function rater()
    {
        return $this->morphTo();
    }

    public function rateable()
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

    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? StrictDataObject::from($value) : null,
        );
    }
}
