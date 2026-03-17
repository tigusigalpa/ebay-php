<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * Contact DTO
 * 
 * Represents a contact with address and phone information
 */
class Contact
{
    public function __construct(
        public readonly ?string $fullName,
        public readonly ?string $companyName,
        public readonly ?ContactAddress $contactAddress,
        public readonly ?PhoneNumber $primaryPhone,
        public readonly ?string $email
    ) {
    }

    /**
     * Create Contact from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fullName: $data['fullName'] ?? null,
            companyName: $data['companyName'] ?? null,
            contactAddress: isset($data['contactAddress']) ? ContactAddress::fromArray($data['contactAddress']) : null,
            primaryPhone: isset($data['primaryPhone']) ? PhoneNumber::fromArray($data['primaryPhone']) : null,
            email: $data['email'] ?? null
        );
    }
}
