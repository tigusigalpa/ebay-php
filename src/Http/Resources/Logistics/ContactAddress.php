<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * ContactAddress DTO
 * 
 * Represents a contact address
 */
class ContactAddress
{
    public function __construct(
        public readonly ?string $addressLine1,
        public readonly ?string $addressLine2,
        public readonly ?string $city,
        public readonly ?string $stateOrProvince,
        public readonly ?string $postalCode,
        public readonly ?string $countryCode
    ) {
    }

    /**
     * Create ContactAddress from array
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
            countryCode: $data['countryCode'] ?? null
        );
    }
}
