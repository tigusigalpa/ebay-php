<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * ShippingQuote DTO
 * 
 * Represents a shipping quote with available rates
 * 
 * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipping_quote/methods/getShippingQuote
 */
class ShippingQuote
{
    public function __construct(
        public readonly ?string $shippingQuoteId,
        public readonly ?string $orderId,
        public readonly ?string $creationDate,
        public readonly ?string $expirationDate,
        public readonly ?PackageSpecification $packageSpecification,
        public readonly ?Contact $shipFrom,
        public readonly ?Contact $shipTo,
        public readonly ?array $rates,
        public readonly ?array $warnings
    ) {
    }

    /**
     * Create ShippingQuote from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $rates = null;
        if (isset($data['rates']) && is_array($data['rates'])) {
            $rates = array_map(fn($rate) => Rate::fromArray($rate), $data['rates']);
        }

        return new self(
            shippingQuoteId: $data['shippingQuoteId'] ?? null,
            orderId: $data['orderId'] ?? null,
            creationDate: $data['creationDate'] ?? null,
            expirationDate: $data['expirationDate'] ?? null,
            packageSpecification: isset($data['packageSpecification']) ? PackageSpecification::fromArray($data['packageSpecification']) : null,
            shipFrom: isset($data['shipFrom']) ? Contact::fromArray($data['shipFrom']) : null,
            shipTo: isset($data['shipTo']) ? Contact::fromArray($data['shipTo']) : null,
            rates: $rates,
            warnings: $data['warnings'] ?? null
        );
    }
}
