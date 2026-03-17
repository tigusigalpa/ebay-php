<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * Insurance DTO
 * 
 * Represents shipping insurance information
 */
class Insurance
{
    public function __construct(
        public readonly ?Amount $additionalCost,
        public readonly ?string $optionType
    ) {
    }

    /**
     * Create Insurance from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            additionalCost: isset($data['additionalCost']) ? Amount::fromArray($data['additionalCost']) : null,
            optionType: $data['optionType'] ?? null
        );
    }
}
