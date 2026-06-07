<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Addresses\Contracts;

use AndyDefer\LaravelFeatures\Addresses\Records\AddressFilterRecord;
use AndyDefer\LaravelFeatures\Addresses\Records\AddressRecord;
use AndyDefer\Repository\AbstractRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

interface AddressRepositoryInterface extends AbstractRepositoryInterface
{
    public function getPrimaryAddress(Model $addressable): ?Model;
    public function getAddressesByType(Model $addressable, string $type): array;
    public function setPrimaryAddress(Model $addressable, int $addressId): void;
    public function findByCriteria(AddressFilterRecord $filter): array;
}
