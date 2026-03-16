<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * MoneyMovement DTO
 * 
 * Represents money movement in a payment dispute
 */
class MoneyMovement
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?Amount $amount,
        public readonly ?string $date
    ) {
    }

    /**
     * Create MoneyMovement from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] ?? null,
            amount: isset($data['amount']) ? Amount::fromArray($data['amount']) : null,
            date: $data['date'] ?? null
        );
    }
}
