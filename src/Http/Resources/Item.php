<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources;

use SimpleXMLElement;
use Tigusigalpa\Ebay\Enums\ListingStatus;
use Tigusigalpa\Ebay\Enums\ListingType;

/**
 * Item/Listing Resource DTO
 */
class Item
{
    public function __construct(
        public readonly string $itemId,
        public readonly string $title,
        public readonly ?ListingStatus $listingStatus,
        public readonly ?ListingType $listingType,
        public readonly ?float $currentPrice,
        public readonly ?string $currencyCode,
        public readonly ?int $quantity,
        public readonly ?int $quantitySold,
        public readonly ?string $startTime,
        public readonly ?string $endTime,
        public readonly ?string $viewItemUrl,
        public readonly array $rawData = []
    ) {
    }

    public static function fromXml(SimpleXMLElement $xml): self
    {
        $listingStatus = isset($xml->SellingStatus->ListingStatus) 
            ? ListingStatus::tryFrom((string) $xml->SellingStatus->ListingStatus) 
            : null;

        $listingType = isset($xml->ListingType) 
            ? ListingType::tryFrom((string) $xml->ListingType) 
            : null;

        return new self(
            itemId: (string) $xml->ItemID,
            title: (string) $xml->Title,
            listingStatus: $listingStatus,
            listingType: $listingType,
            currentPrice: isset($xml->SellingStatus->CurrentPrice) 
                ? (float) $xml->SellingStatus->CurrentPrice 
                : null,
            currencyCode: isset($xml->SellingStatus->CurrentPrice['currencyID']) 
                ? (string) $xml->SellingStatus->CurrentPrice['currencyID'] 
                : null,
            quantity: isset($xml->Quantity) ? (int) $xml->Quantity : null,
            quantitySold: isset($xml->SellingStatus->QuantitySold) 
                ? (int) $xml->SellingStatus->QuantitySold 
                : null,
            startTime: isset($xml->ListingDetails->StartTime) 
                ? (string) $xml->ListingDetails->StartTime 
                : null,
            endTime: isset($xml->ListingDetails->EndTime) 
                ? (string) $xml->ListingDetails->EndTime 
                : null,
            viewItemUrl: isset($xml->ListingDetails->ViewItemURL) 
                ? (string) $xml->ListingDetails->ViewItemURL 
                : null,
            rawData: json_decode(json_encode($xml), true)
        );
    }

    public function toArray(): array
    {
        return [
            'item_id' => $this->itemId,
            'title' => $this->title,
            'listing_status' => $this->listingStatus?->value,
            'listing_type' => $this->listingType?->value,
            'current_price' => $this->currentPrice,
            'currency_code' => $this->currencyCode,
            'quantity' => $this->quantity,
            'quantity_sold' => $this->quantitySold,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'view_item_url' => $this->viewItemUrl,
        ];
    }
}
