<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * Reason for Refund Enum
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/types/sel:ReasonForRefundEnum
 */
enum ReasonForRefundEnum: string
{
    case ADJUST_REFUND = 'ADJUST_REFUND';
    case BUYER_CANCEL = 'BUYER_CANCEL';
    case DEFECTIVE_ITEM = 'DEFECTIVE_ITEM';
    case FOUND_CHEAPER_PRICE = 'FOUND_CHEAPER_PRICE';
    case ITEM_NOT_AS_DESCRIBED = 'ITEM_NOT_AS_DESCRIBED';
    case ITEM_NOT_RECEIVED = 'ITEM_NOT_RECEIVED';
    case ORDER_MISTAKE = 'ORDER_MISTAKE';
    case OUT_OF_STOCK = 'OUT_OF_STOCK';
    case SELLER_CANCEL = 'SELLER_CANCEL';
    case SHIPPING_ADDRESS_UNCONFIRMED = 'SHIPPING_ADDRESS_UNCONFIRMED';
    case TRANSACTION_ERROR = 'TRANSACTION_ERROR';
    case OTHER = 'OTHER';

    public function title(): string
    {
        return match($this) {
            self::ADJUST_REFUND => 'Adjust Refund',
            self::BUYER_CANCEL => 'Buyer Cancel',
            self::DEFECTIVE_ITEM => 'Defective Item',
            self::FOUND_CHEAPER_PRICE => 'Found Cheaper Price',
            self::ITEM_NOT_AS_DESCRIBED => 'Item Not As Described',
            self::ITEM_NOT_RECEIVED => 'Item Not Received',
            self::ORDER_MISTAKE => 'Order Mistake',
            self::OUT_OF_STOCK => 'Out of Stock',
            self::SELLER_CANCEL => 'Seller Cancel',
            self::SHIPPING_ADDRESS_UNCONFIRMED => 'Shipping Address Unconfirmed',
            self::TRANSACTION_ERROR => 'Transaction Error',
            self::OTHER => 'Other',
        };
    }
}
