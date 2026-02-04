<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Listing Type Code Type
 * 
 * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/types/ListingTypeCodeType.html
 */
enum ListingType: string
{
    case AUCTION = 'Chinese';
    case FIXED_PRICE = 'FixedPriceItem';
    case STORES_FIXED_PRICE = 'StoresFixedPrice';
    case PERSONAL_OFFER = 'PersonalOffer';
    case AD_TYPE = 'AdType';
    case LEAD_GENERATION = 'LeadGeneration';

    public function title(): string
    {
        return match($this) {
            self::AUCTION => 'Auction',
            self::FIXED_PRICE => 'Fixed Price',
            self::STORES_FIXED_PRICE => 'Stores Fixed Price',
            self::PERSONAL_OFFER => 'Personal Offer',
            self::AD_TYPE => 'Advertisement',
            self::LEAD_GENERATION => 'Lead Generation',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::AUCTION => 'Competitive bidding format where the highest bidder wins',
            self::FIXED_PRICE => 'Buy It Now format with a fixed price',
            self::STORES_FIXED_PRICE => 'Fixed price listing in an eBay Store',
            self::PERSONAL_OFFER => 'Personal offer to a specific buyer',
            self::AD_TYPE => 'Advertisement listing',
            self::LEAD_GENERATION => 'Lead generation listing',
        };
    }
}
