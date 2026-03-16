<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * PricingSummary DTO
 * 
 * Represents pricing summary for an order
 */
class PricingSummary
{
    public function __construct(
        public readonly ?Amount $priceSubtotal,
        public readonly ?Amount $priceDiscountSubtotal,
        public readonly ?Amount $deliveryCost,
        public readonly ?Amount $deliveryDiscount,
        public readonly ?Amount $tax,
        public readonly ?Amount $total,
        public readonly ?Amount $adjustment
    ) {
    }

    /**
     * Create PricingSummary from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            priceSubtotal: isset($data['priceSubtotal']) ? Amount::fromArray($data['priceSubtotal']) : null,
            priceDiscountSubtotal: isset($data['priceDiscountSubtotal']) ? Amount::fromArray($data['priceDiscountSubtotal']) : null,
            deliveryCost: isset($data['deliveryCost']) ? Amount::fromArray($data['deliveryCost']) : null,
            deliveryDiscount: isset($data['deliveryDiscount']) ? Amount::fromArray($data['deliveryDiscount']) : null,
            tax: isset($data['tax']) ? Amount::fromArray($data['tax']) : null,
            total: isset($data['total']) ? Amount::fromArray($data['total']) : null,
            adjustment: isset($data['adjustment']) ? Amount::fromArray($data['adjustment']) : null
        );
    }
}
