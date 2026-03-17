<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * Dimensions DTO
 * 
 * Represents package dimensions
 */
class Dimensions
{
    public function __construct(
        public readonly ?string $height,
        public readonly ?string $length,
        public readonly ?string $width,
        public readonly ?string $unit
    ) {
    }

    /**
     * Create Dimensions from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            height: $data['height'] ?? null,
            length: $data['length'] ?? null,
            width: $data['width'] ?? null,
            unit: $data['unit'] ?? null
        );
    }
}
