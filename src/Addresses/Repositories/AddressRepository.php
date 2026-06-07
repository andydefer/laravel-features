<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Addresses\Repositories;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\LaravelFeatures\Addresses\Models\Address;
use AndyDefer\LaravelFeatures\Addresses\Records\AddressFilterRecord;
use AndyDefer\LaravelFeatures\Addresses\Records\AddressRecord;
use AndyDefer\Repository\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;

final class AddressRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(
            modelClass: Address::class,
            recordClass: AddressRecord::class,
        );
    }

    protected function applyFilters(Builder $query, AbstractRecord $filters): void
    {
        if (! $filters instanceof AddressFilterRecord) {
            return;
        }

        if ($filters->addressable_type !== null) {
            $query->where('addressable_type', $filters->addressable_type);
        }

        if ($filters->addressable_id !== null) {
            $query->where('addressable_id', $filters->addressable_id);
        }

        if ($filters->address_type !== null) {
            $query->where('address_type', $filters->address_type->value);
        }

        if ($filters->city !== null) {
            $query->where('city', 'like', '%'.$filters->city.'%');
        }

        if ($filters->country !== null) {
            $query->where('country', $filters->country->name);
        }

        if ($filters->postal_code !== null) {
            $query->where('postal_code', $filters->postal_code);
        }
    }
}
