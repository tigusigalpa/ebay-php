<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * Rate DTO
 * 
 * Represents a shipping rate from a carrier
 * 
 * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipping_quote/methods/createShippingQuote
 */
class Rate
{
    public function __construct(
        public readonly ?string $rateId,
        public readonly ?array $rateRecommendation,
        public readonly ?string $carrierId,
        public readonly ?string $carrierLogoUrl,
        public readonly ?string $shippingServiceCode,
        public readonly ?string $shippingServiceName,
        public readonly ?string $shippingCarrierCode,
        public readonly ?array $pickupNetworks,
        public readonly ?array $pickupSlots,
        public readonly ?string $pickupType,
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
     * Create Rate from array
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
            rateRecommendation: $data['rateRecommendation'] ?? null,
            carrierId: $data['carrierId'] ?? null,
            carrierLogoUrl: $data['carrierLogoUrl'] ?? null,
            shippingServiceCode: $data['shippingServiceCode'] ?? null,
            shippingServiceName: $data['shippingServiceName'] ?? null,
            shippingCarrierCode: $data['shippingCarrierCode'] ?? null,
            pickupNetworks: $data['pickupNetworks'] ?? null,
            pickupSlots: $data['pickupSlots'] ?? null,
            pickupType: $data['pickupType'] ?? null,
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
