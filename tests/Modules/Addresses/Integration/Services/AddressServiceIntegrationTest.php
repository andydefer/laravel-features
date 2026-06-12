<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Tests\Integration\Addresses\Services;

use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\LaravelFeatures\Addresses\Enums\AddressType;
use AndyDefer\LaravelFeatures\Addresses\Models\Address;
use AndyDefer\LaravelFeatures\Addresses\Records\AddressRecord;
use AndyDefer\LaravelFeatures\Addresses\Repositories\AddressRepository;
use AndyDefer\LaravelFeatures\Addresses\Services\AddressService;
use AndyDefer\LaravelFeatures\Tests\Fixtures\Models\TestUser;
use AndyDefer\LaravelFeatures\Tests\IntegrationTestCase;
use AndyDefer\PhpVo\Enums\Country;
use AndyDefer\PhpVo\ValueObjects\CoordinatesVO;
use AndyDefer\PhpVo\ValueObjects\PostalCodeVO;
use Illuminate\Support\Collection;

final class AddressServiceIntegrationTest extends IntegrationTestCase
{
    private AddressService $addressService;

    private TestUser $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addressService = new AddressService(
            new AddressRepository
        );

        $this->user = TestUser::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_add_creates_new_address(): void
    {
        // Arrange
        $record = AddressRecord::from([
            'street' => '123 Main St',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]);

        // Act
        $address = $this->addressService->add($this->user, $record);

        // Assert
        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('123 Main St', $address->street);
        $this->assertSame('Paris', $address->city);
        $this->assertSame(Country::FR, $address->country);
        $this->assertSame(AddressType::PRIMARY, $address->address_type);
    }

    public function test_add_with_coordinates_creates_address(): void
    {
        // Arrange
        $coordinates = CoordinatesVO::from([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $record = AddressRecord::from([
            'street' => 'Tour Eiffel',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75007'),
            'address_type' => AddressType::OTHER,
            'geo_coordinates' => $coordinates,
        ]);

        // Act
        $address = $this->addressService->add($this->user, $record);

        // Assert
        $this->assertNotNull($address->coordinates);
        $this->assertSame(48.8566, $address->coordinates->latitude);
        $this->assertSame(2.3522, $address->coordinates->longitude);
    }

    public function test_update_modifies_existing_address(): void
    {
        // Arrange
        $record = AddressRecord::from([
            'street' => '123 Main St',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]);

        $address = $this->addressService->add($this->user, $record);

        $updateRecord = AddressRecord::from([
            'street' => '456 New St',
            'city' => 'Lyon',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('69001'),
        ]);

        // Act
        $updated = $this->addressService->update($address->id, $updateRecord);

        // Assert
        $this->assertSame('456 New St', $updated->street);
        $this->assertSame('Lyon', $updated->city);
        $this->assertSame('69001', $updated->postal_code->getValue());
    }

    public function test_update_raw_updates_with_raw_data_including_null_values(): void
    {
        // Arrange
        $record = AddressRecord::from([
            'street' => 'Original Street',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]);

        $address = $this->addressService->add($this->user, $record);

        // Act - ne pas mettre city à null car la colonne est NOT NULL
        $updated = $this->addressService->updateRaw($address->id, [
            'street' => 'Updated Street',
            'address_type' => AddressType::BILLING->value,
        ]);

        // Assert
        $this->assertSame('Updated Street', $updated->street);
        $this->assertSame('Paris', $updated->city);  // inchangé
        $this->assertSame(AddressType::BILLING, $updated->address_type);
        $this->assertSame(Country::FR, $updated->country);
        $this->assertSame('75001', $updated->postal_code->getValue());

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'street' => 'Updated Street',
            'city' => 'Paris',
            'address_type' => AddressType::BILLING->value,
        ]);
    }

    public function test_update_raw_updates_only_provided_fields_when_passed_partial_data(): void
    {
        // Arrange
        $record = AddressRecord::from([
            'street' => 'Original Street',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]);

        $address = $this->addressService->add($this->user, $record);

        // Act
        $updated = $this->addressService->updateRaw($address->id, [
            'street' => 'Updated Street',
        ]);

        // Assert
        $this->assertSame('Updated Street', $updated->street);
        $this->assertSame('Paris', $updated->city);
        $this->assertSame(AddressType::PRIMARY, $updated->address_type);
        $this->assertSame(Country::FR, $updated->country);
        $this->assertSame('75001', $updated->postal_code->getValue());

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'street' => 'Updated Street',
            'city' => 'Paris',
            'address_type' => AddressType::PRIMARY->value,
        ]);
    }

    public function test_update_raw_sets_metadata_to_null_when_explicitly_provided(): void
    {
        // Arrange
        $metadata = StrictDataObject::from(['key' => 'value']);

        $record = AddressRecord::from([
            'street' => '123 Main St',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
            'metadata' => $metadata,
        ]);

        $address = $this->addressService->add($this->user, $record);

        // Assert - avant mise à jour
        $this->assertNotNull($address->metadata);
        $this->assertSame('value', $address->metadata->get('key'));

        // Act
        $updated = $this->addressService->updateRaw($address->id, [
            'metadata' => null,
        ]);

        // Assert - après mise à jour
        $this->assertNull($updated->metadata);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'metadata' => null,
        ]);
    }

    public function test_update_raw_sets_geo_coordinates_to_null_when_explicitly_provided(): void
    {
        // Arrange
        $coordinates = CoordinatesVO::from([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $record = AddressRecord::from([
            'street' => 'Tour Eiffel',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75007'),
            'address_type' => AddressType::OTHER,
            'geo_coordinates' => $coordinates,
        ]);

        $address = $this->addressService->add($this->user, $record);

        // Assert - avant mise à jour
        $this->assertNotNull($address->coordinates);
        $this->assertSame(48.8566, $address->coordinates->latitude);

        // Act
        $updated = $this->addressService->updateRaw($address->id, [
            'geo_coordinates' => null,
        ]);

        // Assert - après mise à jour
        $this->assertNull($updated->coordinates);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'geo_coordinates' => null,
        ]);
    }

    public function test_update_raw_with_empty_data_does_nothing_and_returns_model(): void
    {
        // Arrange
        $record = AddressRecord::from([
            'street' => 'Original Street',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]);

        $address = $this->addressService->add($this->user, $record);
        $originalUpdatedAt = $address->updated_at;

        // Act
        $updated = $this->addressService->updateRaw($address->id, []);

        // Assert
        $this->assertSame($address->id, $updated->id);
        $this->assertSame('Original Street', $updated->street);
        $this->assertSame('Paris', $updated->city);
        $this->assertSame(AddressType::PRIMARY, $updated->address_type);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'street' => 'Original Street',
            'city' => 'Paris',
        ]);
    }

    public function test_update_raw_throws_exception_when_address_not_found(): void
    {
        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/with id 99999 not found/');

        // Act
        $this->addressService->updateRaw(99999, ['street' => 'New Street']);
    }

    public function test_delete_removes_address(): void
    {
        // Arrange
        $record = AddressRecord::from([
            'street' => '123 Main St',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]);

        $address = $this->addressService->add($this->user, $record);
        $addressId = $address->id;

        // Act
        $deleted = $this->addressService->delete($addressId);

        // Assert
        $this->assertTrue($deleted);
        $this->assertNull($this->addressService->find($addressId));
    }

    public function test_all_returns_collection_of_addresses(): void
    {
        // Arrange
        $this->addressService->add($this->user, AddressRecord::from([
            'street' => '1 Rue A',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]));

        $this->addressService->add($this->user, AddressRecord::from([
            'street' => '2 Rue B',
            'city' => 'Lyon',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('69001'),
            'address_type' => AddressType::BILLING,
        ]));

        // Act
        $addresses = $this->addressService->all($this->user);

        // Assert
        $this->assertInstanceOf(Collection::class, $addresses);
        $this->assertCount(2, $addresses);
    }

    public function test_by_type_returns_filtered_addresses(): void
    {
        // Arrange
        $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Billing Address',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::BILLING,
        ]));

        $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Shipping Address',
            'city' => 'Lyon',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('69001'),
            'address_type' => AddressType::SHIPPING,
        ]));

        // Act
        $billingAddresses = $this->addressService->byType($this->user, AddressType::BILLING);
        $shippingAddresses = $this->addressService->byType($this->user, AddressType::SHIPPING);

        // Assert
        $this->assertCount(1, $billingAddresses);
        $this->assertSame('Billing Address', $billingAddresses->first()->street);
        $this->assertCount(1, $shippingAddresses);
        $this->assertSame('Shipping Address', $shippingAddresses->first()->street);
    }

    public function test_primary_returns_primary_address(): void
    {
        // Arrange
        $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Secondary Address',
            'city' => 'Lyon',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('69001'),
            'address_type' => AddressType::BILLING,
        ]));

        $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Primary Address',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]));

        // Act
        $primary = $this->addressService->primary($this->user);

        // Assert
        $this->assertNotNull($primary);
        $this->assertSame('Primary Address', $primary->street);
        $this->assertSame(AddressType::PRIMARY, $primary->address_type);
    }

    public function test_set_primary_changes_primary_address(): void
    {
        // Arrange
        $address1 = $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Address 1',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::OTHER,
        ]));

        $address2 = $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Address 2',
            'city' => 'Lyon',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('69001'),
            'address_type' => AddressType::OTHER,
        ]));

        // Act
        $this->addressService->setPrimary($this->user, $address2->id);

        $primary = $this->addressService->primary($this->user);
        $address1Reloaded = $this->addressService->find($address1->id);

        // Assert
        $this->assertSame('Address 2', $primary->street);
        $this->assertSame(AddressType::OTHER, $address1Reloaded->address_type);
        $this->assertSame(AddressType::PRIMARY, $primary->address_type);
    }

    public function test_find_returns_address_by_id(): void
    {
        // Arrange
        $record = AddressRecord::from([
            'street' => '123 Main St',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]);

        $address = $this->addressService->add($this->user, $record);

        // Act
        $found = $this->addressService->find($address->id);

        // Assert
        $this->assertNotNull($found);
        $this->assertSame($address->id, $found->id);
        $this->assertSame('123 Main St', $found->street);
    }

    public function test_find_returns_null_for_nonexistent_address(): void
    {
        // Act
        $found = $this->addressService->find(99999);

        // Assert
        $this->assertNull($found);
    }

    public function test_count_returns_number_of_addresses(): void
    {
        // Arrange
        $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Address 1',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]));

        $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Address 2',
            'city' => 'Lyon',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('69001'),
            'address_type' => AddressType::BILLING,
        ]));

        // Act
        $count = $this->addressService->count($this->user);

        // Assert
        $this->assertSame(2, $count);
    }

    public function test_count_returns_zero_when_no_addresses(): void
    {
        // Act
        $count = $this->addressService->count($this->user);

        // Assert
        $this->assertSame(0, $count);
    }

    public function test_has_type_returns_true_when_address_type_exists(): void
    {
        // Arrange
        $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'Billing Address',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::BILLING,
        ]));

        // Act
        $hasBilling = $this->addressService->hasType($this->user, AddressType::BILLING);
        $hasShipping = $this->addressService->hasType($this->user, AddressType::SHIPPING);

        // Assert
        $this->assertTrue($hasBilling);
        $this->assertFalse($hasShipping);
    }

    public function test_has_type_returns_false_when_no_addresses(): void
    {
        // Act
        $hasPrimary = $this->addressService->hasType($this->user, AddressType::PRIMARY);

        // Assert
        $this->assertFalse($hasPrimary);
    }

    public function test_multiple_addresses_for_different_addressables(): void
    {
        // Arrange
        $user2 = TestUser::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->addressService->add($this->user, AddressRecord::from([
            'street' => 'User 1 Address',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
        ]));

        $this->addressService->add($user2, AddressRecord::from([
            'street' => 'User 2 Address',
            'city' => 'Lyon',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('69001'),
            'address_type' => AddressType::PRIMARY,
        ]));

        // Act
        $user1Addresses = $this->addressService->all($this->user);
        $user2Addresses = $this->addressService->all($user2);

        // Assert
        $this->assertCount(1, $user1Addresses);
        $this->assertSame('User 1 Address', $user1Addresses->first()->street);
        $this->assertCount(1, $user2Addresses);
        $this->assertSame('User 2 Address', $user2Addresses->first()->street);
    }

    public function test_add_address_with_metadata(): void
    {
        // Arrange
        $metadata = StrictDataObject::from([
            'floor' => 3,
            'building' => 'Tower A',
            'notes' => 'Ring the bell',
        ]);

        $record = AddressRecord::from([
            'street' => '123 Main St',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
            'metadata' => $metadata,
        ]);

        // Act
        $address = $this->addressService->add($this->user, $record);

        // Assert
        $this->assertNotNull($address->metadata);
        $this->assertSame(3, $address->metadata->floor);
        $this->assertSame('Tower A', $address->metadata->building);
        $this->assertSame('Ring the bell', $address->metadata->notes);
    }

    public function test_update_address_clears_metadata_when_empty_array(): void
    {
        // Arrange
        $record = AddressRecord::from([
            'street' => '123 Main St',
            'city' => 'Paris',
            'country' => Country::FR,
            'postal_code' => PostalCodeVO::from('75001'),
            'address_type' => AddressType::PRIMARY,
            'metadata' => StrictDataObject::from(['key' => 'value']),
        ]);

        $address = $this->addressService->add($this->user, $record);

        $updateRecord = AddressRecord::from([
            'metadata' => StrictDataObject::from([]),  // ← tableau vide au lieu de null
        ]);

        // Act
        $updated = $this->addressService->update($address->id, $updateRecord);

        // Assert
        $this->assertNotNull($updated->metadata);
        $this->assertEmpty($updated->metadata->toArray());
    }
}
