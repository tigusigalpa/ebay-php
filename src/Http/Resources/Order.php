<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources;

use SimpleXMLElement;
use Tigusigalpa\Ebay\Enums\OrderStatus;
use Tigusigalpa\Ebay\Enums\PaymentStatus;

/**
 * Order Resource DTO
 */
class Order
{
    public function __construct(
        public readonly string $orderId,
        public readonly ?OrderStatus $orderStatus,
        public readonly ?PaymentStatus $paymentStatus,
        public readonly float $total,
        public readonly string $currencyCode,
        public readonly ?string $buyerUserId,
        public readonly ?string $createdTime,
        public readonly array $transactions = [],
        public readonly array $rawData = []
    ) {
    }

    public static function fromXml(SimpleXMLElement $xml): self
    {
        $orderStatus = isset($xml->OrderStatus) 
            ? OrderStatus::tryFrom((string) $xml->OrderStatus) 
            : null;

        $paymentStatus = isset($xml->CheckoutStatus->Status) 
            ? PaymentStatus::tryFrom((string) $xml->CheckoutStatus->Status) 
            : null;

        $transactions = [];
        if (isset($xml->TransactionArray->Transaction)) {
            foreach ($xml->TransactionArray->Transaction as $transaction) {
                $transactions[] = [
                    'transaction_id' => (string) ($transaction->TransactionID ?? ''),
                    'item_id' => (string) ($transaction->Item->ItemID ?? ''),
                    'title' => (string) ($transaction->Item->Title ?? ''),
                    'quantity' => (int) ($transaction->QuantityPurchased ?? 0),
                    'transaction_price' => (float) ($transaction->TransactionPrice ?? 0),
                ];
            }
        }

        return new self(
            orderId: (string) $xml->OrderID,
            orderStatus: $orderStatus,
            paymentStatus: $paymentStatus,
            total: (float) ($xml->Total ?? 0),
            currencyCode: (string) ($xml->Total['currencyID'] ?? 'USD'),
            buyerUserId: (string) ($xml->BuyerUserID ?? null),
            createdTime: (string) ($xml->CreatedTime ?? null),
            transactions: $transactions,
            rawData: json_decode(json_encode($xml), true)
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            orderId: $data['orderId'] ?? $data['order_id'] ?? '',
            orderStatus: isset($data['orderStatus']) 
                ? OrderStatus::tryFrom($data['orderStatus']) 
                : null,
            paymentStatus: isset($data['paymentStatus']) 
                ? PaymentStatus::tryFrom($data['paymentStatus']) 
                : null,
            total: (float) ($data['total'] ?? $data['pricingSummary']['total']['value'] ?? 0),
            currencyCode: $data['currencyCode'] ?? $data['pricingSummary']['total']['currency'] ?? 'USD',
            buyerUserId: $data['buyerUserId'] ?? $data['buyer']['username'] ?? null,
            createdTime: $data['createdTime'] ?? $data['creationDate'] ?? null,
            transactions: $data['transactions'] ?? $data['lineItems'] ?? [],
            rawData: $data
        );
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'order_status' => $this->orderStatus?->value,
            'payment_status' => $this->paymentStatus?->value,
            'total' => $this->total,
            'currency_code' => $this->currencyCode,
            'buyer_user_id' => $this->buyerUserId,
            'created_time' => $this->createdTime,
            'transactions' => $this->transactions,
        ];
    }
}
