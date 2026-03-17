<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * AdditionalOption DTO
 * 
 * Represents an additional shipping option
 */
class AdditionalOption
{
    public function __construct(
        public readonly ?Amount $additionalCost,
        public readonly ?string $optionType
    ) {
    }

    /**
     * Create AdditionalOption from array
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
