<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Country Code Type
 * 
 * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/types/CountryCodeType.html
 */
enum Country: string
{
    case US = 'US';
    case CA = 'CA';
    case GB = 'GB';
    case AU = 'AU';
    case AT = 'AT';
    case BE = 'BE';
    case FR = 'FR';
    case DE = 'DE';
    case IT = 'IT';
    case NL = 'NL';
    case ES = 'ES';
    case CH = 'CH';
    case HK = 'HK';
    case IN = 'IN';
    case IE = 'IE';
    case MY = 'MY';
    case PH = 'PH';
    case PL = 'PL';
    case SG = 'SG';
    case CN = 'CN';
    case JP = 'JP';
    case SE = 'SE';

    public function title(): string
    {
        return match($this) {
            self::US => 'United States',
            self::CA => 'Canada',
            self::GB => 'United Kingdom',
            self::AU => 'Australia',
            self::AT => 'Austria',
            self::BE => 'Belgium',
            self::FR => 'France',
            self::DE => 'Germany',
            self::IT => 'Italy',
            self::NL => 'Netherlands',
            self::ES => 'Spain',
            self::CH => 'Switzerland',
            self::HK => 'Hong Kong',
            self::IN => 'India',
            self::IE => 'Ireland',
            self::MY => 'Malaysia',
            self::PH => 'Philippines',
            self::PL => 'Poland',
            self::SG => 'Singapore',
            self::CN => 'China',
            self::JP => 'Japan',
            self::SE => 'Sweden',
        };
    }
}
