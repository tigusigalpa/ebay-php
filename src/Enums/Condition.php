<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Item Condition Code Type
 * 
 * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/types/ConditionCodeType.html
 */
enum Condition: int
{
    case NEW = 1000;
    case NEW_WITH_TAGS = 1500;
    case NEW_WITHOUT_TAGS = 1750;
    case NEW_WITH_DEFECTS = 2000;
    case MANUFACTURER_REFURBISHED = 2010;
    case SELLER_REFURBISHED = 2500;
    case LIKE_NEW = 2750;
    case USED_EXCELLENT = 3000;
    case USED_VERY_GOOD = 4000;
    case USED_GOOD = 5000;
    case USED_ACCEPTABLE = 6000;
    case FOR_PARTS_NOT_WORKING = 7000;

    public function title(): string
    {
        return match($this) {
            self::NEW => 'New',
            self::NEW_WITH_TAGS => 'New with tags',
            self::NEW_WITHOUT_TAGS => 'New without tags',
            self::NEW_WITH_DEFECTS => 'New with defects',
            self::MANUFACTURER_REFURBISHED => 'Manufacturer refurbished',
            self::SELLER_REFURBISHED => 'Seller refurbished',
            self::LIKE_NEW => 'Like New',
            self::USED_EXCELLENT => 'Used - Excellent',
            self::USED_VERY_GOOD => 'Used - Very Good',
            self::USED_GOOD => 'Used - Good',
            self::USED_ACCEPTABLE => 'Used - Acceptable',
            self::FOR_PARTS_NOT_WORKING => 'For parts or not working',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::NEW => 'A brand-new, unused, unopened, undamaged item in its original packaging',
            self::NEW_WITH_TAGS => 'A brand-new, unused item with tags attached',
            self::NEW_WITHOUT_TAGS => 'A brand-new, unused item without tags',
            self::NEW_WITH_DEFECTS => 'A new item with minor defects or cosmetic imperfections',
            self::MANUFACTURER_REFURBISHED => 'Professionally restored to working order by the manufacturer',
            self::SELLER_REFURBISHED => 'Professionally restored to working order by the seller',
            self::LIKE_NEW => 'An item in excellent condition with no signs of wear',
            self::USED_EXCELLENT => 'An item that has been used but is in excellent condition',
            self::USED_VERY_GOOD => 'An item that has been used but is in very good condition',
            self::USED_GOOD => 'An item that has been used and shows signs of wear',
            self::USED_ACCEPTABLE => 'An item that has been heavily used but is still functional',
            self::FOR_PARTS_NOT_WORKING => 'An item that does not function as intended',
        };
    }
}
