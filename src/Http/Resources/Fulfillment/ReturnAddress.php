<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * ReturnAddress DTO
 * 
 * Represents a return address for disputed items
 */
class ReturnAddress
{
    public function __construct(
        public readonly ?string $addressLine1,
        public readonly ?string $addressLine2,
        public readonly ?string $city,
        public readonly ?string $stateOrProvince,
        public readonly ?string $postalCode,
        public readonly ?string $countryCode,
        public readonly ?string $fullName,
        public readonly ?string $primaryPhone,
        public readonly ?string $email
    ) {
    }

    /**
     * Create ReturnAddress from array
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
            fullName: $data['fullName'] ?? null,
            primaryPhone: $data['primaryPhone'] ?? null,
            email: $data['email'] ?? null
        );
    }
}
