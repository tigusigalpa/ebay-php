<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Site Code Type
 * 
 * @link https://developer.ebay.com/devzone/XML/docs/Reference/eBay/types/SiteCodeType.html
 * @link https://developer.ebay.com/api-docs/static/rest-request-components.html#marketpl
 * @link https://developer.ebay.com/devzone/Product/Concepts/SiteIDToGlobalID.html
 */
enum Site: int
{
    case US = 0;
    case CANADA = 2;
    case UK = 3;
    case AUSTRALIA = 15;
    case AUSTRIA = 16;
    case BELGIUM_FRENCH = 23;
    case FRANCE = 71;
    case GERMANY = 77;
    case ITALY = 101;
    case BELGIUM_DUTCH = 123;
    case NETHERLANDS = 146;
    case SPAIN = 186;
    case SWITZERLAND = 193;
    case HONG_KONG = 201;
    case INDIA = 203;
    case IRELAND = 205;
    case MALAYSIA = 207;
    case CANADA_FRENCH = 210;
    case PHILIPPINES = 211;
    case POLAND = 212;
    case SINGAPORE = 216;

    public function title(): string
    {
        return match($this) {
            self::US => 'United States',
            self::CANADA => 'Canada',
            self::UK => 'Great Britain',
            self::AUSTRALIA => 'Australia',
            self::AUSTRIA => 'Austria',
            self::BELGIUM_FRENCH => 'Belgium (Française)',
            self::FRANCE => 'France',
            self::GERMANY => 'Germany',
            self::ITALY => 'Italy',
            self::BELGIUM_DUTCH => 'Belgium (Nederlandse)',
            self::NETHERLANDS => 'Netherlands',
            self::SPAIN => 'Spain',
            self::SWITZERLAND => 'Switzerland',
            self::HONG_KONG => 'Hong Kong',
            self::INDIA => 'India',
            self::IRELAND => 'Ireland',
            self::MALAYSIA => 'Malaysia',
            self::CANADA_FRENCH => 'Canada (Française)',
            self::PHILIPPINES => 'Philippines',
            self::POLAND => 'Poland',
            self::SINGAPORE => 'Singapore',
        };
    }

    public function code(): string
    {
        return match($this) {
            self::US => 'us',
            self::CANADA => 'ca',
            self::UK => 'uk',
            self::AUSTRALIA => 'au',
            self::AUSTRIA => 'at',
            self::BELGIUM_FRENCH => 'befr',
            self::FRANCE => 'fr',
            self::GERMANY => 'de',
            self::ITALY => 'it',
            self::BELGIUM_DUTCH => 'benl',
            self::NETHERLANDS => 'nl',
            self::SPAIN => 'es',
            self::SWITZERLAND => 'ch',
            self::HONG_KONG => 'hk',
            self::INDIA => 'in',
            self::IRELAND => 'ie',
            self::MALAYSIA => 'my',
            self::CANADA_FRENCH => 'cafr',
            self::PHILIPPINES => 'ph',
            self::POLAND => 'pl',
            self::SINGAPORE => 'sg',
        };
    }

    public function url(): string
    {
        return match($this) {
            self::US => 'https://ebay.com',
            self::CANADA => 'https://ebay.ca',
            self::UK => 'https://ebay.co.uk',
            self::AUSTRALIA => 'https://ebay.com.au',
            self::AUSTRIA => 'https://ebay.at',
            self::BELGIUM_FRENCH => 'https://befr.ebay.be',
            self::FRANCE => 'https://ebay.fr',
            self::GERMANY => 'https://ebay.de',
            self::ITALY => 'https://ebay.it',
            self::BELGIUM_DUTCH => 'https://benl.ebay.be',
            self::NETHERLANDS => 'https://ebay.nl',
            self::SPAIN => 'https://ebay.es',
            self::SWITZERLAND => 'https://ebay.ch',
            self::HONG_KONG => 'https://ebay.com.hk',
            self::INDIA => 'https://ebay.in',
            self::IRELAND => 'https://ebay.ie',
            self::MALAYSIA => 'https://ebay.com.my',
            self::CANADA_FRENCH => 'https://cafr.ebay.ca',
            self::PHILIPPINES => 'https://ebay.ph',
            self::POLAND => 'https://ebay.pl',
            self::SINGAPORE => 'https://ebay.com.sg',
        };
    }

    public function currency(): Currency
    {
        return match($this) {
            self::US => Currency::USD,
            self::CANADA, self::CANADA_FRENCH => Currency::CAD,
            self::UK => Currency::GBP,
            self::AUSTRALIA => Currency::AUD,
            self::AUSTRIA, self::BELGIUM_FRENCH, self::BELGIUM_DUTCH, 
            self::FRANCE, self::GERMANY, self::ITALY, self::NETHERLANDS, 
            self::SPAIN, self::IRELAND => Currency::EUR,
            self::SWITZERLAND => Currency::CHF,
            self::HONG_KONG => Currency::HKD,
            self::INDIA => Currency::INR,
            self::MALAYSIA => Currency::MYR,
            self::PHILIPPINES => Currency::PHP,
            self::POLAND => Currency::PLN,
            self::SINGAPORE => Currency::SGD,
        };
    }

    public function locale(): string
    {
        return match($this) {
            self::US => 'en-US',
            self::CANADA => 'en-CA',
            self::UK => 'en-GB',
            self::AUSTRALIA => 'en-AU',
            self::AUSTRIA => 'de-AT',
            self::BELGIUM_FRENCH => 'fr-BE',
            self::BELGIUM_DUTCH => 'nl-BE',
            self::FRANCE => 'fr-FR',
            self::GERMANY => 'de-DE',
            self::ITALY => 'it-IT',
            self::NETHERLANDS => 'nl-NL',
            self::SPAIN => 'es-ES',
            self::SWITZERLAND => 'de-CH',
            self::HONG_KONG => 'zh-HK',
            self::INDIA => 'en-IN',
            self::IRELAND => 'en-IE',
            self::MALAYSIA => 'en-US',
            self::CANADA_FRENCH => 'fr-CA',
            self::PHILIPPINES => 'en-PH',
            self::POLAND => 'pl-PL',
            self::SINGAPORE => 'en-US',
        };
    }

    public function marketplace(): string
    {
        return match($this) {
            self::US => 'EBAY_US',
            self::CANADA, self::CANADA_FRENCH => 'EBAY_CA',
            self::UK => 'EBAY_GB',
            self::AUSTRALIA => 'EBAY_AU',
            self::AUSTRIA => 'EBAY_AT',
            self::BELGIUM_FRENCH, self::BELGIUM_DUTCH => 'EBAY_BE',
            self::FRANCE => 'EBAY_FR',
            self::GERMANY => 'EBAY_DE',
            self::ITALY => 'EBAY_IT',
            self::NETHERLANDS => 'EBAY_NL',
            self::SPAIN => 'EBAY_ES',
            self::SWITZERLAND => 'EBAY_CH',
            self::HONG_KONG => 'EBAY_HK',
            self::INDIA => 'EBAY_IN',
            self::IRELAND => 'EBAY_IE',
            self::MALAYSIA => 'EBAY_MY',
            self::PHILIPPINES => 'EBAY_PH',
            self::POLAND => 'EBAY_PL',
            self::SINGAPORE => 'EBAY_SG',
        };
    }

    public function language(): string
    {
        return match($this) {
            self::US => 'en_US',
            self::CANADA => 'en_CA',
            self::UK => 'en_GB',
            self::AUSTRALIA => 'en_AU',
            self::AUSTRIA => 'de_AT',
            self::BELGIUM_FRENCH => 'fr_BE',
            self::BELGIUM_DUTCH => 'nl_BE',
            self::FRANCE => 'fr_FR',
            self::GERMANY => 'de_DE',
            self::ITALY => 'it_IT',
            self::NETHERLANDS => 'nl_NL',
            self::SPAIN => 'es_ES',
            self::SWITZERLAND => 'de_CH',
            self::HONG_KONG => 'zh_HK',
            self::INDIA => 'en_IN',
            self::IRELAND => 'en_IE',
            self::MALAYSIA => 'en_US',
            self::CANADA_FRENCH => 'fr_CA',
            self::PHILIPPINES => 'en_PH',
            self::POLAND => 'pl_PL',
            self::SINGAPORE => 'en_US',
        };
    }

    public static function fromCode(string $code): ?self
    {
        foreach (self::cases() as $site) {
            if ($site->code() === strtolower($code)) {
                return $site;
            }
        }
        return null;
    }

    public static function fromMarketplace(string $marketplace): ?self
    {
        foreach (self::cases() as $site) {
            if ($site->marketplace() === strtoupper($marketplace)) {
                return $site;
            }
        }
        return null;
    }
}
