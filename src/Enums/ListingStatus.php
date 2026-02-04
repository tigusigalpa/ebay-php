<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Listing Status Code Type
 * 
 * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/types/ListingStatusCodeType.html
 */
enum ListingStatus: string
{
    case ACTIVE = 'Active';
    case COMPLETED = 'Completed';
    case ENDED = 'Ended';

    public function title(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::ENDED => 'Ended',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::ACTIVE => 'The listing is still active or the listing has ended with a sale but eBay has not completed processing the sale details (e.g., total price and high bidder). A multi-item listing is considered active until all items have winning bids or purchases or the listing ends with at least one winning bid or purchase. If the listing has ended with a sale but this Active status is returned, please allow several minutes for eBay to finish processing the listing.',
            self::COMPLETED => 'The listing has closed and eBay has completed processing the sale. All sale information returned from eBay (e.g., total price and high bidder) should be considered accurate and complete. Although the Final Value Fee (FVF) for FixedPriceItem and StoresFixedPrice items is returned by GetSellerTransactions and GetItemTransactions, all other listing types (excluding Buy It Now purchases) require the listing status to be Completed before the Final Value Fee is returned.',
            self::ENDED => 'The listing has ended. If the listing ended with a sale, eBay has completed processing of the sale. All sale information returned from eBay (e.g., total price and high bidder) should be considered accurate and complete. However, the final value fee is not yet available.',
        };
    }
}
