<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * DisputeLineItem DTO
 * 
 * Represents a line item in a payment dispute
 */
class DisputeLineItem
{
    public function __construct(
        public readonly ?string $itemId,
        public readonly ?string $lineItemId,
        public readonly ?string $title,
        public readonly ?int $quantity
    ) {
    }

    /**
     * Create DisputeLineItem from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            itemId: $data['itemId'] ?? null,
            lineItemId: $data['lineItemId'] ?? null,
            title: $data['title'] ?? null,
            quantity: $data['quantity'] ?? null
        );
    }
}
