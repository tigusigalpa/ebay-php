<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * Order DTO
 * 
 * Represents a fulfillment order from the eBay Fulfillment API
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/methods/getOrder
 */
class Order
{
    public function __construct(
        public readonly string $orderId,
        public readonly ?string $legacyOrderId,
        public readonly ?string $creationDate,
        public readonly ?string $lastModifiedDate,
        public readonly ?string $orderFulfillmentStatus,
        public readonly ?string $orderPaymentStatus,
        public readonly ?string $sellerId,
        public readonly ?Buyer $buyer,
        public readonly ?PricingSummary $pricingSummary,
        public readonly ?PaymentSummary $paymentSummary,
        public readonly ?FulfillmentStartInstruction $fulfillmentStartInstructions,
        public readonly ?array $lineItems,
        public readonly ?string $salesRecordReference,
        public readonly ?Amount $totalFeeBasisAmount,
        public readonly ?Amount $totalMarketplaceFee,
        public readonly ?array $cancelStatus
    ) {
    }

    /**
     * Create Order from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $lineItems = null;
        if (isset($data['lineItems']) && is_array($data['lineItems'])) {
            $lineItems = array_map(fn($item) => LineItem::fromArray($item), $data['lineItems']);
        }

        $fulfillmentStartInstructions = null;
        if (isset($data['fulfillmentStartInstructions']) && is_array($data['fulfillmentStartInstructions'])) {
            $fulfillmentStartInstructions = array_map(
                fn($instruction) => FulfillmentStartInstruction::fromArray($instruction),
                $data['fulfillmentStartInstructions']
            );
        }

        return new self(
            orderId: $data['orderId'] ?? '',
            legacyOrderId: $data['legacyOrderId'] ?? null,
            creationDate: $data['creationDate'] ?? null,
            lastModifiedDate: $data['lastModifiedDate'] ?? null,
            orderFulfillmentStatus: $data['orderFulfillmentStatus'] ?? null,
            orderPaymentStatus: $data['orderPaymentStatus'] ?? null,
            sellerId: $data['sellerId'] ?? null,
            buyer: isset($data['buyer']) ? Buyer::fromArray($data['buyer']) : null,
            pricingSummary: isset($data['pricingSummary']) ? PricingSummary::fromArray($data['pricingSummary']) : null,
            paymentSummary: isset($data['paymentSummary']) ? PaymentSummary::fromArray($data['paymentSummary']) : null,
            fulfillmentStartInstructions: $fulfillmentStartInstructions,
            lineItems: $lineItems,
            salesRecordReference: $data['salesRecordReference'] ?? null,
            totalFeeBasisAmount: isset($data['totalFeeBasisAmount']) ? Amount::fromArray($data['totalFeeBasisAmount']) : null,
            totalMarketplaceFee: isset($data['totalMarketplaceFee']) ? Amount::fromArray($data['totalMarketplaceFee']) : null,
            cancelStatus: $data['cancelStatus'] ?? null
        );
    }
}
