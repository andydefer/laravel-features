<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Addresses\Models;

use AndyDefer\DomainStructures\Services\EnumService;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Addresses\Enums\AddressType;
use AndyDefer\PhpVo\Enums\Country;
use AndyDefer\PhpVo\ValueObjects\CoordinatesVO;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
use AndyDefer\PhpVo\ValueObjects\PostalCodeVO;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Address extends Model
{
    use SoftDeletes;

    private static ?EnumService $enumService = null;

    protected $table = 'addresses';

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'street',
        'city',
        'country',
        'postal_code',
        'geo_coordinates',
        'address_type',
        'metadata',
    ];

    protected $casts = [
        'geo_coordinates' => 'array',
        'metadata' => 'array',
        'country' => Country::class,
        'address_type' => AddressType::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    private static function getEnumService(): EnumService
    {
        if (self::$enumService === null) {
            self::$enumService = new EnumService;
        }

        return self::$enumService;
    }

    public function addressable()
    {
        return $this->morphTo();
    }

    // Accesseur pour postal_code (propriété virtuelle postalCode en camelCase)
    protected function postalCode(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => isset($attributes['postal_code'])
                ? PostalCodeVO::from($attributes['postal_code'])
                : null,
        );
    }

    // Accesseur pour geo_coordinates (propriété virtuelle coordinates en camelCase)
    protected function coordinates(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (! isset($attributes['geo_coordinates']) || ! $attributes['geo_coordinates']) {
                    return null;
                }

                $coords = is_string($attributes['geo_coordinates'])
                    ? json_decode($attributes['geo_coordinates'], true)
                    : $attributes['geo_coordinates'];

                return CoordinatesVO::from([
                    'latitude' => $coords['latitude'] ?? null,
                    'longitude' => $coords['longitude'] ?? null,
                ]);
            }
        );
    }

    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => isset($attributes['metadata']) && $attributes['metadata']
                ? StrictDataObject::from(
                    is_string($attributes['metadata'])
                        ? json_decode($attributes['metadata'], true)
                        : $attributes['metadata']
                )
                : null,
        );
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
}
