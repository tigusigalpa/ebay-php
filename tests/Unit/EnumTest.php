<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tigusigalpa\Ebay\Enums\Currency;
use Tigusigalpa\Ebay\Enums\ListingStatus;
use Tigusigalpa\Ebay\Enums\Site;

class EnumTest extends TestCase
{
    public function test_site_enum_returns_correct_values(): void
    {
        $site = Site::US;
        
        $this->assertEquals(0, $site->value);
        $this->assertEquals('United States', $site->title());
        $this->assertEquals('us', $site->code());
        $this->assertEquals('https://ebay.com', $site->url());
        $this->assertEquals('en-US', $site->locale());
        $this->assertEquals('EBAY_US', $site->marketplace());
        $this->assertEquals(Currency::USD, $site->currency());
    }

    public function test_site_from_code(): void
    {
        $site = Site::fromCode('uk');
        
        $this->assertNotNull($site);
        $this->assertEquals(Site::UK, $site);
        $this->assertEquals('Great Britain', $site->title());
    }

    public function test_site_from_marketplace(): void
    {
        $site = Site::fromMarketplace('EBAY_DE');
        
        $this->assertNotNull($site);
        $this->assertEquals(Site::GERMANY, $site);
        $this->assertEquals('Germany', $site->title());
    }

    public function test_currency_enum_returns_correct_symbols(): void
    {
        $this->assertEquals('$', Currency::USD->symbol());
        $this->assertEquals('£', Currency::GBP->symbol());
        $this->assertEquals('€', Currency::EUR->symbol());
        $this->assertEquals('¥', Currency::CNY->symbol());
    }

    public function test_currency_enum_returns_correct_html_entities(): void
    {
        $this->assertEquals('&#36;', Currency::USD->htmlEntity());
        $this->assertEquals('&#163;', Currency::GBP->htmlEntity());
        $this->assertEquals('&#8364;', Currency::EUR->htmlEntity());
    }

    public function test_listing_status_enum(): void
    {
        $status = ListingStatus::ACTIVE;
        
        $this->assertEquals('Active', $status->value);
        $this->assertEquals('Active', $status->title());
        $this->assertNotEmpty($status->description());
    }

    public function test_all_sites_have_required_methods(): void
    {
        foreach (Site::cases() as $site) {
            $this->assertIsString($site->title());
            $this->assertIsString($site->code());
            $this->assertIsString($site->url());
            $this->assertIsString($site->locale());
            $this->assertIsString($site->marketplace());
            $this->assertInstanceOf(Currency::class, $site->currency());
            $this->assertIsString($site->language());
        }
    }
}
