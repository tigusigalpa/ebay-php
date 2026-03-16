<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * LineItem DTO
 * 
 * Represents a line item in an order
 */
class LineItem
{
    public function __construct(
        public readonly string $lineItemId,
        public readonly ?string $legacyItemId,
        public readonly ?string $legacyVariationId,
        public readonly ?string $title,
        public readonly ?string $sku,
        public readonly ?Amount $lineItemCost,
        public readonly ?int $quantity,
        public readonly ?string $lineItemFulfillmentStatus,
        public readonly ?DeliveryCost $deliveryCost,
        public readonly ?array $appliedPromotions,
        public readonly ?array $taxes,
        public readonly ?array $refunds,
        public readonly ?string $itemLocation,
        public readonly ?array $properties,
        public readonly ?string $soldFormat,
        public readonly ?string $purchaseMarketplaceId
    ) {
    }

    /**
     * Create LineItem from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            lineItemId: $data['lineItemId'] ?? '',
            legacyItemId: $data['legacyItemId'] ?? null,
            legacyVariationId: $data['legacyVariationId'] ?? null,
            title: $data['title'] ?? null,
            sku: $data['sku'] ?? null,
            lineItemCost: isset($data['lineItemCost']) ? Amount::fromArray($data['lineItemCost']) : null,
            quantity: $data['quantity'] ?? null,
            lineItemFulfillmentStatus: $data['lineItemFulfillmentStatus'] ?? null,
            deliveryCost: isset($data['deliveryCost']) ? DeliveryCost::fromArray($data['deliveryCost']) : null,
            appliedPromotions: $data['appliedPromotions'] ?? null,
            taxes: $data['taxes'] ?? null,
            refunds: $data['refunds'] ?? null,
            itemLocation: $data['itemLocation'] ?? null,
            properties: $data['properties'] ?? null,
            soldFormat: $data['soldFormat'] ?? null,
            purchaseMarketplaceId: $data['purchaseMarketplaceId'] ?? null
        );
    }
}
