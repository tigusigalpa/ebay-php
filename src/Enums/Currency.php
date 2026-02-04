<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Currency Code Type
 * 
 * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/types/CurrencyCodeType.html
 */
enum Currency: string
{
    case USD = 'USD';
    case CAD = 'CAD';
    case GBP = 'GBP';
    case AUD = 'AUD';
    case EUR = 'EUR';
    case CHF = 'CHF';
    case CNY = 'CNY';
    case HKD = 'HKD';
    case PHP = 'PHP';
    case PLN = 'PLN';
    case SEK = 'SEK';
    case SGD = 'SGD';
    case TWD = 'TWD';
    case MYR = 'MYR';
    case INR = 'INR';

    public function title(): string
    {
        return match($this) {
            self::USD => 'US Dollar',
            self::CAD => 'Canadian Dollar',
            self::GBP => 'British Pound',
            self::AUD => 'Australian Dollar',
            self::EUR => 'Euro',
            self::CHF => 'Swiss Franc',
            self::CNY => 'Chinese Renminbi',
            self::HKD => 'Hong Kong Dollar',
            self::PHP => 'Philippines Peso',
            self::PLN => 'Polish Zloty',
            self::SEK => 'Sweden Krona',
            self::SGD => 'Singapore Dollar',
            self::TWD => 'Taiwanese Dollar',
            self::MYR => 'Malaysian Ringgit',
            self::INR => 'Indian Rupee',
        };
    }

    public function symbol(): string
    {
        return match($this) {
            self::USD, self::CAD, self::AUD, self::SGD, self::HKD, self::TWD => '$',
            self::GBP => '£',
            self::EUR => '€',
            self::CHF => 'Fr',
            self::CNY => '¥',
            self::PHP => '₱',
            self::PLN => 'zł',
            self::SEK => 'kr',
            self::MYR => 'RM',
            self::INR => '₹',
        };
    }

    public function htmlEntity(): string
    {
        return match($this) {
            self::USD, self::CAD, self::AUD, self::SGD => '&#36;',
            self::GBP => '&#163;',
            self::EUR => '&#8364;',
            self::CHF => '&#8355;',
            self::CNY, self::HKD => '&#20803;',
            self::PHP => '&#8369;',
            self::PLN => '&#122;&#322;',
            self::SEK => '&#107;&#114;',
            self::TWD => '&#78;&#84;&#36;',
            self::MYR => '&#82;&#77;',
            self::INR => '&#8377;',
        };
    }
}
