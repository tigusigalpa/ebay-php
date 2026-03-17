<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * PackageSpecification DTO
 * 
 * Represents package specifications including dimensions and weight
 */
class PackageSpecification
{
    public function __construct(
        public readonly ?Dimensions $dimensions,
        public readonly ?Weight $weight
    ) {
    }

    /**
     * Create PackageSpecification from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            dimensions: isset($data['dimensions']) ? Dimensions::fromArray($data['dimensions']) : null,
            weight: isset($data['weight']) ? Weight::fromArray($data['weight']) : null
        );
    }
}
