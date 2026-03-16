<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * Buyer DTO
 * 
 * Represents buyer information
 */
class Buyer
{
    public function __construct(
        public readonly ?string $username,
        public readonly ?string $taxIdentifier,
        public readonly ?string $buyerRegistrationAddress
    ) {
    }

    /**
     * Create Buyer from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'] ?? null,
            taxIdentifier: $data['taxIdentifier'] ?? null,
            buyerRegistrationAddress: $data['buyerRegistrationAddress'] ?? null
        );
    }
}
