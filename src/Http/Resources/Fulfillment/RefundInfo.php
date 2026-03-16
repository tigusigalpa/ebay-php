<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * RefundInfo DTO
 * 
 * Represents refund information
 */
class RefundInfo
{
    public function __construct(
        public readonly ?string $refundId,
        public readonly ?Amount $refundAmount,
        public readonly ?string $refundDate,
        public readonly ?string $refundStatus,
        public readonly ?string $refundReferenceId
    ) {
    }

    /**
     * Create RefundInfo from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            refundId: $data['refundId'] ?? null,
            refundAmount: isset($data['refundAmount']) ? Amount::fromArray($data['refundAmount']) : null,
            refundDate: $data['refundDate'] ?? null,
            refundStatus: $data['refundStatus'] ?? null,
            refundReferenceId: $data['refundReferenceId'] ?? null
        );
    }
}
