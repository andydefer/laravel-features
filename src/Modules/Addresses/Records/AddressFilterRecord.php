<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Addresses\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\LaravelFeatures\Modules\Addresses\Enums\AddressType;
use AndyDefer\PhpVo\Enums\Country;

final class AddressFilterRecord extends AbstractRecord
{
    public function __construct(
        public readonly ?string $addressable_type = null,
        public readonly ?int $addressable_id = null,
        public readonly ?AddressType $address_type = null,
        public readonly ?string $city = null,
        public readonly ?Country $country = null,
        public readonly ?string $postal_code = null,
    ) {}
}
