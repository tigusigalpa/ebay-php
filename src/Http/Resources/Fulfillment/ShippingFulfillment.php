<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * ShippingFulfillment DTO
 * 
 * Represents a shipping fulfillment
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/shipping_fulfillment/methods/getShippingFulfillment
 */
class ShippingFulfillment
{
    public function __construct(
        public readonly ?string $fulfillmentId,
        public readonly ?array $lineItems,
        public readonly ?string $shipmentTrackingNumber,
        public readonly ?string $shippingCarrierCode,
        public readonly ?string $shippedDate,
        public readonly ?string $shippingServiceCode
    ) {
    }

    /**
     * Create ShippingFulfillment from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $lineItems = null;
        if (isset($data['lineItems']) && is_array($data['lineItems'])) {
            $lineItems = array_map(fn($item) => LineItemReference::fromArray($item), $data['lineItems']);
        }

        return new self(
            fulfillmentId: $data['fulfillmentId'] ?? null,
            lineItems: $lineItems,
            shipmentTrackingNumber: $data['shipmentTrackingNumber'] ?? null,
            shippingCarrierCode: $data['shippingCarrierCode'] ?? null,
            shippedDate: $data['shippedDate'] ?? null,
            shippingServiceCode: $data['shippingServiceCode'] ?? null
        );
    }
}
