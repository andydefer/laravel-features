<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Modules\Addresses\Records;

use AndyDefer\DomainStructures\Abstracts\AbstractRecord;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Modules\Addresses\Enums\AddressType;
use AndyDefer\PhpVo\Enums\Country;
use AndyDefer\PhpVo\ValueObjects\CoordinatesVO;
use AndyDefer\PhpVo\ValueObjects\DateTimeVO;
use AndyDefer\PhpVo\ValueObjects\PostalCodeVO;

final class AddressRecord extends AbstractRecord
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $addressable_type = null,
        public readonly ?int $addressable_id = null,
        public readonly ?string $street = null,
        public readonly ?string $city = null,
        public readonly ?Country $country = null,
        public readonly ?PostalCodeVO $postal_code = null,
        public readonly ?CoordinatesVO $geo_coordinates = null,
        public readonly ?AddressType $address_type = null,
        public readonly ?StrictDataObject $metadata = null,
        public readonly ?DateTimeVO $created_at = null,
        public readonly ?DateTimeVO $updated_at = null,
        public readonly ?DateTimeVO $deleted_at = null,
    ) {}
}
