<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Conversation Status
 * 
 * @link https://developer.ebay.com/api-docs/commerce/message/types/api:ConversationStatusEnum
 */
enum ConversationStatus: string
{
    case ACTIVE = 'ACTIVE';
    case ARCHIVED = 'ARCHIVED';
    case DELETED = 'DELETED';
    case READ = 'READ';
    case UNREAD = 'UNREAD';

    public function title(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::ARCHIVED => 'Archived',
            self::DELETED => 'Deleted',
            self::READ => 'Read',
            self::UNREAD => 'Unread',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::ACTIVE => 'Conversation is active and visible',
            self::ARCHIVED => 'Conversation has been archived',
            self::DELETED => 'Conversation has been deleted',
            self::READ => 'Conversation has been read',
            self::UNREAD => 'Conversation has unread messages',
        };
    }
}
