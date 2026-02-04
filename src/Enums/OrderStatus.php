<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Order Status Code Type
 * 
 * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/types/OrderStatusCodeType.html
 */
enum OrderStatus: string
{
    case ACTIVE = 'Active';
    case AUTHENTICATED = 'Authenticated';
    case CANCELLED = 'Cancelled';
    case COMPLETED = 'Completed';
    case DEFAULT = 'Default';
    case INACTIVE = 'Inactive';
    case IN_PROCESS = 'InProcess';
    case INVALID = 'Invalid';
    case SHIPPED = 'Shipped';

    public function title(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::AUTHENTICATED => 'Authenticated',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
            self::DEFAULT => 'Default',
            self::INACTIVE => 'Inactive',
            self::IN_PROCESS => 'In Process',
            self::INVALID => 'Invalid',
            self::SHIPPED => 'Shipped',
        };
    }
}
