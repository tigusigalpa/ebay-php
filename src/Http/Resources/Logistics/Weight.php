<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * Weight DTO
 * 
 * Represents package weight
 */
class Weight
{
    public function __construct(
        public readonly ?string $value,
        public readonly ?string $unit
    ) {
    }

    /**
     * Create Weight from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'] ?? null,
            unit: $data['unit'] ?? null
        );
    }
}
