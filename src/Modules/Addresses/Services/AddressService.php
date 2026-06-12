<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Addresses\Services;

use AndyDefer\DomainStructures\Services\HydrationService;
use AndyDefer\LaravelFeatures\Modules\Addresses\Enums\AddressType;
use AndyDefer\LaravelFeatures\Modules\Addresses\Records\AddressFilterRecord;
use AndyDefer\LaravelFeatures\Modules\Addresses\Records\AddressRecord;
use AndyDefer\LaravelFeatures\Modules\Addresses\Repositories\AddressRepository;
use AndyDefer\Repository\Records\FindByRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class AddressService
{
    public function __construct(
        private readonly AddressRepository $addressRepository,
        private readonly HydrationService $hydration,
    ) {}

    public function add(Model $addressable, AddressRecord $record): Model
    {
        $record = $this->hydration->hydrate(AddressRecord::class, [
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            ...$record->toArrayWithoutNulls(),
        ]);

        return $this->addressRepository->create($record);
    }

    public function update(int $addressId, AddressRecord $record): Model
    {
        return $this->addressRepository->update($addressId, $record);
    }

    /**
     * Update an address with raw array data.
     * Use this when you need to set fields to NULL in the database.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateRaw(int $addressId, array $data): Model
    {
        return $this->addressRepository->updateRaw($addressId, $data);
    }

    public function delete(int $addressId): bool
    {
        return $this->addressRepository->delete($addressId);
    }

    public function all(Model $addressable): Collection
    {
        $filter = $this->hydration->hydrate(AddressFilterRecord::class, [
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
        ]);

        $findByRecord = new FindByRecord(filters: $filter);

        return $this->addressRepository->findBy($findByRecord);
    }

    public function byType(Model $addressable, AddressType $type): Collection
    {
        $filter = $this->hydration->hydrate(AddressFilterRecord::class, [
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            'address_type' => $type,
        ]);

        $findByRecord = new FindByRecord(filters: $filter);

        return $this->addressRepository->findBy($findByRecord);
    }

    public function primary(Model $addressable): ?Model
    {
        $filter = $this->hydration->hydrate(AddressFilterRecord::class, [
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            'address_type' => AddressType::PRIMARY,
        ]);

        $findByRecord = new FindByRecord(
            filters: $filter,
            limit: 1,
        );

        $collection = $this->addressRepository->findBy($findByRecord);

        return $collection->first();
    }

    public function setPrimary(Model $addressable, int $addressId): void
    {
        $oldPrimary = $this->primary($addressable);

        if ($oldPrimary) {
            $oldPrimaryRecord = $this->hydration->hydrate(AddressRecord::class, [
                'address_type' => AddressType::OTHER,
            ]);
            $this->addressRepository->update($oldPrimary->id, $oldPrimaryRecord);
        }

        $newPrimaryRecord = $this->hydration->hydrate(AddressRecord::class, [
            'address_type' => AddressType::PRIMARY,
        ]);
        $this->addressRepository->update($addressId, $newPrimaryRecord);
    }

    public function find(int $addressId): ?Model
    {
        return $this->addressRepository->find($addressId);
    }

    public function count(Model $addressable): int
    {
        $filter = $this->hydration->hydrate(AddressFilterRecord::class, [
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
        ]);

        return $this->addressRepository->count($filter);
    }

    public function hasType(Model $addressable, AddressType $type): bool
    {
        $filter = $this->hydration->hydrate(AddressFilterRecord::class, [
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            'address_type' => $type,
        ]);

        return $this->addressRepository->exists($filter);
    }
}
