<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

use Tigusigalpa\Ebay\Enums\DisputeStatusEnum;

/**
 * PaymentDisputeSummary DTO
 * 
 * Represents a payment dispute summary
 */
class PaymentDisputeSummary
{
    public function __construct(
        public readonly ?string $paymentDisputeId,
        public readonly ?string $orderId,
        public readonly ?string $openDate,
        public readonly ?string $respondByDate,
        public readonly ?DisputeStatusEnum $paymentDisputeStatus,
        public readonly ?Amount $amount,
        public readonly ?string $reason,
        public readonly ?string $buyerUsername
    ) {
    }

    /**
     * Create PaymentDisputeSummary from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            paymentDisputeId: $data['paymentDisputeId'] ?? null,
            orderId: $data['orderId'] ?? null,
            openDate: $data['openDate'] ?? null,
            respondByDate: $data['respondByDate'] ?? null,
            paymentDisputeStatus: isset($data['paymentDisputeStatus']) ? DisputeStatusEnum::tryFrom($data['paymentDisputeStatus']) : null,
            amount: isset($data['amount']) ? Amount::fromArray($data['amount']) : null,
            reason: $data['reason'] ?? null,
            buyerUsername: $data['buyerUsername'] ?? null
        );
    }
}
