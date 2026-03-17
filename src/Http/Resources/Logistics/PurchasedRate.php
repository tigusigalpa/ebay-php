<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * PurchasedRate DTO
 * 
 * Represents a purchased shipping rate
 * 
 * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipment/methods/createFromShippingQuote
 */
class PurchasedRate
{
    public function __construct(
        public readonly ?string $rateId,
        public readonly ?string $carrierId,
        public readonly ?string $shippingServiceCode,
        public readonly ?string $shippingCarrierCode,
        public readonly ?Amount $shippingCost,
        public readonly ?string $shippingCostType,
        public readonly ?array $additionalOptions,
        public readonly ?Insurance $insurance,
        public readonly ?string $maxEstimatedDeliveryDate,
        public readonly ?string $minEstimatedDeliveryDate,
        public readonly ?string $rateExpirationDate,
        public readonly ?string $baseRateExpirationDate
    ) {
    }

    /**
     * Create PurchasedRate from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $additionalOptions = null;
        if (isset($data['additionalOptions']) && is_array($data['additionalOptions'])) {
            $additionalOptions = array_map(fn($option) => AdditionalOption::fromArray($option), $data['additionalOptions']);
        }

        return new self(
            rateId: $data['rateId'] ?? null,
            carrierId: $data['carrierId'] ?? null,
            shippingServiceCode: $data['shippingServiceCode'] ?? null,
            shippingCarrierCode: $data['shippingCarrierCode'] ?? null,
            shippingCost: isset($data['shippingCost']) ? Amount::fromArray($data['shippingCost']) : null,
            shippingCostType: $data['shippingCostType'] ?? null,
            additionalOptions: $additionalOptions,
            insurance: isset($data['insurance']) ? Insurance::fromArray($data['insurance']) : null,
            maxEstimatedDeliveryDate: $data['maxEstimatedDeliveryDate'] ?? null,
            minEstimatedDeliveryDate: $data['minEstimatedDeliveryDate'] ?? null,
            rateExpirationDate: $data['rateExpirationDate'] ?? null,
            baseRateExpirationDate: $data['baseRateExpirationDate'] ?? null
        );
    }
}
