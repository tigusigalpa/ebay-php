<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * ShippingFulfillmentPagedCollection DTO
 * 
 * Represents a paginated collection of shipping fulfillments
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/shipping_fulfillment/methods/getShippingFulfillments
 */
class ShippingFulfillmentPagedCollection
{
    public function __construct(
        public readonly array $fulfillments,
        public readonly ?int $total,
        public readonly ?array $warnings
    ) {
    }

    /**
     * Create ShippingFulfillmentPagedCollection from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $fulfillments = [];
        if (isset($data['fulfillments']) && is_array($data['fulfillments'])) {
            $fulfillments = array_map(fn($fulfillment) => ShippingFulfillment::fromArray($fulfillment), $data['fulfillments']);
        }

        return new self(
            fulfillments: $fulfillments,
            total: $data['total'] ?? null,
            warnings: $data['warnings'] ?? null
        );
    }
}
