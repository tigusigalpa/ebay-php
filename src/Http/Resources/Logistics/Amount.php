<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * Amount DTO
 * 
 * Represents a monetary amount with currency
 */
class Amount
{
    public function __construct(
        public readonly ?string $currency,
        public readonly ?string $value
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
            currency: $data['currency'] ?? null,
            value: $data['value'] ?? null
        );
    }
}
