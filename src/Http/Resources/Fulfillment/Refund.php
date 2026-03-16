<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * Refund DTO
 * 
 * Represents a refund response
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/methods/issueRefund
 */
class Refund
{
    public function __construct(
        public readonly ?string $refundId,
        public readonly ?string $refundStatus,
        public readonly ?Amount $refundAmount
    ) {
    }

    /**
     * Create Refund from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            refundId: $data['refundId'] ?? null,
            refundStatus: $data['refundStatus'] ?? null,
            refundAmount: isset($data['refundAmount']) ? Amount::fromArray($data['refundAmount']) : null
        );
    }
}
