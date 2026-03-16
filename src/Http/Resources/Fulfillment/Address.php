<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * Address DTO
 * 
 * Represents a physical address
 */
class Address
{
    public function __construct(
        public readonly ?string $addressLine1,
        public readonly ?string $addressLine2,
        public readonly ?string $city,
        public readonly ?string $stateOrProvince,
        public readonly ?string $postalCode,
        public readonly ?string $countryCode,
        public readonly ?string $county,
        public readonly ?string $fullName
    ) {
    }

    /**
     * Create Address from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            addressLine1: $data['addressLine1'] ?? null,
            addressLine2: $data['addressLine2'] ?? null,
            city: $data['city'] ?? null,
            stateOrProvince: $data['stateOrProvince'] ?? null,
            postalCode: $data['postalCode'] ?? null,
            countryCode: $data['countryCode'] ?? null,
            county: $data['county'] ?? null,
            fullName: $data['fullName'] ?? null
        );
    }
}
