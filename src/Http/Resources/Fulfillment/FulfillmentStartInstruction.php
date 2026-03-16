<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * FulfillmentStartInstruction DTO
 * 
 * Represents fulfillment start instructions
 */
class FulfillmentStartInstruction
{
    public function __construct(
        public readonly ?string $fulfillmentInstructionsType,
        public readonly ?string $minEstimatedDeliveryDate,
        public readonly ?string $maxEstimatedDeliveryDate,
        public readonly ?Address $shippingStep,
        public readonly ?string $ebaySupportedFulfillment
    ) {
    }

    /**
     * Create FulfillmentStartInstruction from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $shippingStep = null;
        if (isset($data['shippingStep']['shipTo'])) {
            $shippingStep = Address::fromArray($data['shippingStep']['shipTo']);
        }

        return new self(
            fulfillmentInstructionsType: $data['fulfillmentInstructionsType'] ?? null,
            minEstimatedDeliveryDate: $data['minEstimatedDeliveryDate'] ?? null,
            maxEstimatedDeliveryDate: $data['maxEstimatedDeliveryDate'] ?? null,
            shippingStep: $shippingStep,
            ebaySupportedFulfillment: $data['ebaySupportedFulfillment'] ?? null
        );
    }
}
