<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * Payment Dispute Status Enum
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/types/api:DisputeStatusEnum
 */
enum DisputeStatusEnum: string
{
    case OPEN = 'OPEN';
    case WAITING_FOR_SELLER_RESPONSE = 'WAITING_FOR_SELLER_RESPONSE';
    case WAITING_FOR_BUYER_RESPONSE = 'WAITING_FOR_BUYER_RESPONSE';
    case CLOSED = 'CLOSED';
    case RESOLVED = 'RESOLVED';
    case UNDER_REVIEW = 'UNDER_REVIEW';
    case APPEALED = 'APPEALED';
    case CLOSED_WITH_REFUND = 'CLOSED_WITH_REFUND';
    case CLOSED_WITHOUT_REFUND = 'CLOSED_WITHOUT_REFUND';

    public function title(): string
    {
        return match($this) {
            self::OPEN => 'Open',
            self::WAITING_FOR_SELLER_RESPONSE => 'Waiting for Seller Response',
            self::WAITING_FOR_BUYER_RESPONSE => 'Waiting for Buyer Response',
            self::CLOSED => 'Closed',
            self::RESOLVED => 'Resolved',
            self::UNDER_REVIEW => 'Under Review',
            self::APPEALED => 'Appealed',
            self::CLOSED_WITH_REFUND => 'Closed with Refund',
            self::CLOSED_WITHOUT_REFUND => 'Closed without Refund',
        };
    }
}
