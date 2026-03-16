<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * PaymentSummary DTO
 * 
 * Represents payment summary for an order
 */
class PaymentSummary
{
    public function __construct(
        public readonly ?Amount $totalDueSeller,
        public readonly ?array $payments,
        public readonly ?array $refunds
    ) {
    }

    /**
     * Create PaymentSummary from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $refunds = null;
        if (isset($data['refunds']) && is_array($data['refunds'])) {
            $refunds = array_map(fn($refund) => RefundInfo::fromArray($refund), $data['refunds']);
        }

        return new self(
            totalDueSeller: isset($data['totalDueSeller']) ? Amount::fromArray($data['totalDueSeller']) : null,
            payments: $data['payments'] ?? null,
            refunds: $refunds
        );
    }
}
