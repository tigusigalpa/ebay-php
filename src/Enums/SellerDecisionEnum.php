<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * Seller Decision Enum
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/types/api:SellerDecisionEnum
 */
enum SellerDecisionEnum: string
{
    case ACCEPT = 'ACCEPT';
    case CONTEST = 'CONTEST';

    public function title(): string
    {
        return match($this) {
            self::ACCEPT => 'Accept',
            self::CONTEST => 'Contest',
        };
    }
}
