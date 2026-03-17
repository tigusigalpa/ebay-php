<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Logistics;

/**
 * Shipment DTO
 * 
 * Represents a shipment created from a shipping quote
 * 
 * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipment/methods/getShipment
 */
class Shipment
{
    public function __construct(
        public readonly ?string $shipmentId,
        public readonly ?string $shipmentTrackingNumber,
        public readonly ?string $labelCustomMessage,
        public readonly ?string $labelSize,
        public readonly ?string $labelStatus,
        public readonly ?string $creationDate,
        public readonly ?string $labelExpirationDate,
        public readonly ?string $labelPurchaseFailureReason,
        public readonly ?PackageSpecification $packageSpecification,
        public readonly ?PurchasedRate $rate,
        public readonly ?Contact $shipFrom,
        public readonly ?Contact $shipTo,
        public readonly ?Contact $returnTo
    ) {
    }

    /**
     * Create Shipment from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            shipmentId: $data['shipmentId'] ?? null,
            shipmentTrackingNumber: $data['shipmentTrackingNumber'] ?? null,
            labelCustomMessage: $data['labelCustomMessage'] ?? null,
            labelSize: $data['labelSize'] ?? null,
            labelStatus: $data['labelStatus'] ?? null,
            creationDate: $data['creationDate'] ?? null,
            labelExpirationDate: $data['labelExpirationDate'] ?? null,
            labelPurchaseFailureReason: $data['labelPurchaseFailureReason'] ?? null,
            packageSpecification: isset($data['packageSpecification']) ? PackageSpecification::fromArray($data['packageSpecification']) : null,
            rate: isset($data['rate']) ? PurchasedRate::fromArray($data['rate']) : null,
            shipFrom: isset($data['shipFrom']) ? Contact::fromArray($data['shipFrom']) : null,
            shipTo: isset($data['shipTo']) ? Contact::fromArray($data['shipTo']) : null,
            returnTo: isset($data['returnTo']) ? Contact::fromArray($data['returnTo']) : null
        );
    }
}
