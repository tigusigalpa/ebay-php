<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * LineItemReference DTO
 * 
 * Represents a reference to a line item
 */
class LineItemReference
{
    public function __construct(
        public readonly string $lineItemId,
        public readonly int $quantity
    ) {
    }

    /**
     * Create LineItemReference from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            lineItemId: $data['lineItemId'] ?? '',
            quantity: $data['quantity'] ?? 0
        );
    }
}
