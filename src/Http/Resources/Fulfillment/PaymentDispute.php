<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

use Tigusigalpa\Ebay\Enums\DisputeStatusEnum;

/**
 * PaymentDispute DTO
 * 
 * Represents a payment dispute
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/getPaymentDispute
 */
class PaymentDispute
{
    public function __construct(
        public readonly ?string $paymentDisputeId,
        public readonly ?string $orderId,
        public readonly ?string $openDate,
        public readonly ?string $respondByDate,
        public readonly ?DisputeStatusEnum $paymentDisputeStatus,
        public readonly ?Amount $amount,
        public readonly ?string $reason,
        public readonly ?string $buyerUsername,
        public readonly ?int $revision,
        public readonly ?array $lineItems,
        public readonly ?array $evidence,
        public readonly ?string $closedDate,
        public readonly ?string $resolution,
        public readonly ?MoneyMovement $moneyMovement
    ) {
    }

    /**
     * Create PaymentDispute from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $lineItems = null;
        if (isset($data['lineItems']) && is_array($data['lineItems'])) {
            $lineItems = array_map(fn($item) => DisputeLineItem::fromArray($item), $data['lineItems']);
        }

        $evidence = null;
        if (isset($data['evidence']) && is_array($data['evidence'])) {
            $evidence = array_map(fn($ev) => DisputeEvidence::fromArray($ev), $data['evidence']);
        }

        return new self(
            paymentDisputeId: $data['paymentDisputeId'] ?? null,
            orderId: $data['orderId'] ?? null,
            openDate: $data['openDate'] ?? null,
            respondByDate: $data['respondByDate'] ?? null,
            paymentDisputeStatus: isset($data['paymentDisputeStatus']) ? DisputeStatusEnum::tryFrom($data['paymentDisputeStatus']) : null,
            amount: isset($data['amount']) ? Amount::fromArray($data['amount']) : null,
            reason: $data['reason'] ?? null,
            buyerUsername: $data['buyerUsername'] ?? null,
            revision: $data['revision'] ?? null,
            lineItems: $lineItems,
            evidence: $evidence,
            closedDate: $data['closedDate'] ?? null,
            resolution: $data['resolution'] ?? null,
            moneyMovement: isset($data['moneyMovement']) ? MoneyMovement::fromArray($data['moneyMovement']) : null
        );
    }
}
