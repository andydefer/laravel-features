<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Addresses\Services;

use AndyDefer\LaravelFeatures\Addresses\Enums\AddressType;
use AndyDefer\LaravelFeatures\Addresses\Records\AddressFilterRecord;
use AndyDefer\LaravelFeatures\Addresses\Records\AddressRecord;
use AndyDefer\LaravelFeatures\Addresses\Repositories\AddressRepository;
use AndyDefer\Repository\Records\FindByRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class AddressService
{
    public function __construct(
        private readonly AddressRepository $addressRepository,
    ) {}

    public function add(Model $addressable, AddressRecord $record): Model
    {
        $record = AddressRecord::from([
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
     * @param int $addressId
     * @param array<string, mixed> $data
     * @return Model
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
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
        ]);

        $findByRecord = new FindByRecord(filters: $filter);

        return $this->addressRepository->findBy($findByRecord);
    }

    public function byType(Model $addressable, AddressType $type): Collection
    {
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            'address_type' => $type,
        ]);

        $findByRecord = new FindByRecord(filters: $filter);

        return $this->addressRepository->findBy($findByRecord);
    }

    public function primary(Model $addressable): ?Model
    {
        $filter = AddressFilterRecord::from([
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
            $this->addressRepository->update($oldPrimary->id, AddressRecord::from([
                'address_type' => AddressType::OTHER,
            ]));
        }

        $this->addressRepository->update($addressId, AddressRecord::from([
            'address_type' => AddressType::PRIMARY,
        ]));
    }

    public function find(int $addressId): ?Model
    {
        return $this->addressRepository->find($addressId);
    }

    public function count(Model $addressable): int
    {
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
        ]);

        return $this->addressRepository->count($filter);
    }

    public function hasType(Model $addressable, AddressType $type): bool
    {
        $filter = AddressFilterRecord::from([
            'addressable_type' => $addressable->getMorphClass(),
            'addressable_id' => $addressable->getKey(),
            'address_type' => $type,
        ]);

        return $this->addressRepository->exists($filter);
    }
}
