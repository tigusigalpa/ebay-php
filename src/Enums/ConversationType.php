<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Conversation Type
 * 
 * @link https://developer.ebay.com/api-docs/commerce/message/types/api:ConversationTypeEnum
 */
enum ConversationType: string
{
    case FROM_EBAY = 'FROM_EBAY';
    case FROM_MEMBERS = 'FROM_MEMBERS';

    public function title(): string
    {
        return match($this) {
            self::FROM_EBAY => 'From eBay',
            self::FROM_MEMBERS => 'From Members',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::FROM_EBAY => 'Messages sent from eBay to the seller',
            self::FROM_MEMBERS => 'Messages between eBay members (buyer-seller communication)',
        };
    }
}
