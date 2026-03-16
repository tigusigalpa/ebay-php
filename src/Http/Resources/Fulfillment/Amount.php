<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * Amount DTO
 * 
 * Represents a monetary amount with currency
 */
class Amount
{
    public function __construct(
        public readonly string $value,
        public readonly string $currency
    ) {
    }

    /**
     * Create Amount from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'] ?? '0.00',
            currency: $data['currency'] ?? 'USD'
        );
    }
}
