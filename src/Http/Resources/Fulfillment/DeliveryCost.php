<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * DeliveryCost DTO
 * 
 * Represents delivery cost information
 */
class DeliveryCost
{
    public function __construct(
        public readonly ?Amount $shippingCost,
        public readonly ?Amount $shippingIntermediationFee,
        public readonly ?Amount $importCharges,
        public readonly ?Amount $additionalDeliveryCostPerUnit
    ) {
    }

    /**
     * Create DeliveryCost from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            shippingCost: isset($data['shippingCost']) ? Amount::fromArray($data['shippingCost']) : null,
            shippingIntermediationFee: isset($data['shippingIntermediationFee']) ? Amount::fromArray($data['shippingIntermediationFee']) : null,
            importCharges: isset($data['importCharges']) ? Amount::fromArray($data['importCharges']) : null,
            additionalDeliveryCostPerUnit: isset($data['additionalDeliveryCostPerUnit']) ? Amount::fromArray($data['additionalDeliveryCostPerUnit']) : null
        );
    }
}
