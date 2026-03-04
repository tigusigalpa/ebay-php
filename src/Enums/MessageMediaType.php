<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Message Media Type
 * 
 * @link https://developer.ebay.com/api-docs/commerce/message/types/api:MessageMediaTypeEnum
 */
enum MessageMediaType: string
{
    case IMAGE = 'IMAGE';
    case PDF = 'PDF';
    case DOC = 'DOC';
    case TXT = 'TXT';

    public function title(): string
    {
        return match($this) {
            self::IMAGE => 'Image',
            self::PDF => 'PDF Document',
            self::DOC => 'Document',
            self::TXT => 'Text File',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::IMAGE => 'Image file attachment (JPG, PNG, GIF, etc.)',
            self::PDF => 'PDF document attachment',
            self::DOC => 'Document file attachment (DOC, DOCX, etc.)',
            self::TXT => 'Plain text file attachment',
        };
    }
}
