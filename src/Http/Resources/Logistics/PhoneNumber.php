<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * PhoneNumber DTO
 * 
 * Represents a phone number
 */
class PhoneNumber
{
    public function __construct(
        public readonly ?string $phoneNumber
    ) {
    }

    /**
     * Create PhoneNumber from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            phoneNumber: $data['phoneNumber'] ?? null
        );
    }
}
