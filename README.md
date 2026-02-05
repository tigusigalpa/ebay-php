# eBay PHP/Laravel SDK

![eBay PHP SDK](https://github.com/user-attachments/assets/629586b3-1a78-4919-98e0-f8fd3cef57e0)

[![Latest Version](https://img.shields.io/packagist/v/tigusigalpa/ebay-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![License](https://img.shields.io/packagist/l/tigusigalpa/ebay-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![PHP Version](https://img.shields.io/packagist/php-v/tigusigalpa/ebay-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![Laravel](https://img.shields.io/badge/Laravel-9%2B-FF2D20.svg?style=flat-square&logo=Laravel)](https://laravel.com)
[![eBay](https://img.shields.io/badge/eBay-API-0064D2.svg?style=flat-square&logo=eBay)](https://developer.ebay.com)

PHP SDK for eBay API. Supports Trading API (XML) and Commerce API (REST), OAuth 2.0 authentication, 20+ marketplaces. Works as standalone and with Laravel.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Authentication](#authentication)
- [Trading API](#trading-api)
- [Commerce API](#commerce-api)
- [Working with Marketplaces](#working-with-marketplaces)
- [Enums and DTOs](#enums-and-dtos)
- [Error Handling](#error-handling)
- [Optimization](#optimization)
- [Testing](#testing)
- [FAQ](#faq)
- [Support](#support)
- [License](#license)

## Features

**API:**
- Trading API (XML) — orders, listings, categories, GetMyEbaySelling, etc.
- Commerce API (REST) — inventory, fulfillment, taxonomy, translation
- OAuth 2.0 with automatic token refresh
- 20+ marketplaces (US, UK, DE, FR, AU, etc.)

**Code:**
- PHP 8.1+ with native Enums
- Strict typing
- Immutable DTOs
- Fluent interface
- PSR-4, PSR-7, PSR-12

**Laravel:**
- Service Provider + Facade
- Dependency Injection
- .env configuration

## Requirements

- PHP 8.1+
- Guzzle 7.x
- Laravel 9.x / 10.x / 11.x / 12.x (optional)
- Composer

## Installation

```bash
composer require tigusigalpa/ebay-php
```

For Laravel — publish config:

```bash
php artisan vendor:publish --tag=ebay-config
```

Add credentials to `.env`:

```env
EBAY_ENVIRONMENT=sandbox

# Sandbox
EBAY_SANDBOX_APP_ID=your-sandbox-app-id
EBAY_SANDBOX_CERT_ID=your-sandbox-cert-id
EBAY_SANDBOX_DEV_ID=your-sandbox-dev-id
EBAY_SANDBOX_RUNAME=your-sandbox-runame

# Production
EBAY_PRODUCTION_APP_ID=your-production-app-id
EBAY_PRODUCTION_CERT_ID=your-production-cert-id
EBAY_PRODUCTION_DEV_ID=your-production-dev-id
EBAY_PRODUCTION_RUNAME=your-production-runame

EBAY_DEFAULT_SITE=US
```

Get credentials at [eBay Developers Program](https://developer.ebay.com/).

## Quick Start

### Laravel (Facade)

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Tigusigalpa\Ebay\Enums\Site;

// OAuth URL
$url = Ebay::getConsentUrl();

// Exchange code for tokens
$tokens = Ebay::exchangeCodeForToken($code);

// Get orders
$orders = Ebay::trading()->getOrders([
    'CreateTimeFrom' => now()->subDays(30)->toIso8601String(),
]);

// Switch marketplace
Ebay::setSite(Site::UK)->trading()->getOrders();
```

### Standalone PHP

```php
require_once 'vendor/autoload.php';

use Tigusigalpa\Ebay\Ebay;
use Tigusigalpa\Ebay\Enums\Site;

$ebay = new Ebay([
    'environment' => 'sandbox',
    'sandbox' => [
        'app_id' => 'your-app-id',
        'cert_id' => 'your-cert-id',
        'dev_id' => 'your-dev-id',
        'runame' => 'your-runame',
    ],
    'site' => Site::US,
]);

$orders = $ebay->trading()->getOrders();
```

## Authentication

### OAuth 2.0 Flow

**1. Get authorization URL:**

```php
$consentUrl = Ebay::getConsentUrl(
    scopes: config('ebay.scopes'),
    state: 'your-state-parameter',
    locale: 'en-US'
);

return redirect($consentUrl);
```

**2. Handle callback:**

```php
public function callback(Request $request)
{
    $code = $request->get('code');
    $tokenData = Ebay::exchangeCodeForToken($code);
    
    // Store tokens
    auth()->user()->update([
        'ebay_access_token' => $tokenData['access_token'],
        'ebay_access_token_expires_at' => $tokenData['expires_at'],
        'ebay_refresh_token' => $tokenData['refresh_token'],
        'ebay_refresh_token_expires_at' => $tokenData['refresh_token_expires_at'],
    ]);
    
    return redirect()->route('dashboard');
}
```

**3. Use tokens:**

```php
$user = auth()->user();

Ebay::setAccessToken($user->ebay_access_token, $user->ebay_access_token_expires_at);
Ebay::setRefreshToken($user->ebay_refresh_token, $user->ebay_refresh_token_expires_at);

// Tokens refresh automatically when expired
$orders = Ebay::trading()->getOrders();
```

## Trading API

### Orders

```php
$xml = Ebay::trading()->getOrders([
    'CreateTimeFrom' => '2024-01-01T00:00:00.000Z',
    'CreateTimeTo' => '2024-12-31T23:59:59.999Z',
    'OrderStatus' => 'Active',
]);

foreach ($xml->OrderArray->Order as $order) {
    $orderId = (string) $order->OrderID;
    $total = (float) $order->Total;
}
```

### Items

```php
// Get item
$item = Ebay::trading()->getItem('123456789');
$title = (string) $item->Item->Title;
$price = (float) $item->Item->SellingStatus->CurrentPrice;

// Create listing
$response = Ebay::trading()->addFixedPriceItem([
    'Title' => 'My Product Title',
    'Description' => 'Product description',
    'PrimaryCategory' => ['CategoryID' => '12345'],
    'StartPrice' => 99.99,
    'Quantity' => 10,
    'Currency' => 'USD',
    'Country' => 'US',
    'Location' => 'New York',
    'DispatchTimeMax' => 3,
    'ShippingDetails' => [
        'ShippingType' => 'Flat',
        'ShippingServiceOptions' => [
            'ShippingService' => 'USPSPriority',
            'ShippingServiceCost' => 5.00,
        ],
    ],
]);

$itemId = (string) $response->ItemID;
```

### Categories

```php
$categories = Ebay::trading()->getCategories([
    'CategorySiteID' => 0,
    'LevelLimit' => 2,
]);
```

## Commerce API

### Inventory

```php
// Get
$item = Ebay::commerce()->getInventoryItem('SKU-123');

// Update quantity
Ebay::commerce()->createOrReplaceInventoryItem($sku, [
    'availability' => ['shipToLocationAvailability' => ['quantity' => $newQuantity]],
]);
```

### Fulfillment

```php
$orders = Ebay::commerce()->getFulfillmentOrders([
    'filter' => 'orderfulfillmentstatus:{NOT_STARTED|IN_PROGRESS}',
    'limit' => 50,
]);
```

### Translation

```php
$translated = Ebay::commerce()->translate(
    text: 'Brand New iPhone',
    fromLanguage: 'en',
    toLanguage: 'de',
    context: 'ITEM_TITLE'
);
```

### Category Aspects

```php
$aspects = Ebay::commerce()->getItemAspectsForCategory('0', '12345');

foreach ($aspects['aspects'] as $aspect) {
    echo $aspect['localizedAspectName'];
}
```

## Working with Marketplaces

```php
use Tigusigalpa\Ebay\Enums\Site;

// Set marketplace
Ebay::setSite(Site::UK);
Ebay::setSite(Site::GERMANY);
Ebay::setSite(Site::AUSTRALIA);

// Marketplace information
$site = Site::US;
$site->title();        // "United States"
$site->code();         // "us"
$site->url();          // "https://ebay.com"
$site->locale();       // "en-US"
$site->marketplace();  // "EBAY_US"
$site->currency()->symbol(); // "$"

// Find by code
$site = Site::fromCode('uk');           // Site::UK
$site = Site::fromMarketplace('EBAY_DE'); // Site::GERMANY

// List on multiple marketplaces
foreach ([Site::UK, Site::GERMANY, Site::FRANCE] as $site) {
    Ebay::setSite($site)->trading()->addFixedPriceItem($itemData);
}
```

## Enums and DTOs

Package uses PHP 8.1 Enums for type safety:

```php
use Tigusigalpa\Ebay\Enums\{Site, Currency, ListingStatus, OrderStatus, PaymentStatus, ListingType};

// Currency
$currency = Currency::USD;
$currency->symbol();      // "$"
$currency->htmlEntity();  // "&#36;"
$currency->title();       // "US Dollar"

// Listing Status
$status = ListingStatus::ACTIVE;
$status->title();         // "Active"
$status->description();

// Listing Type
$listingType = ListingType::FIXED_PRICE;
$listingType->description(); // "Buy It Now format with a fixed price"
```

### DTOs

```php
use Tigusigalpa\Ebay\Http\Resources\{Order, Item};

$xml = Ebay::trading()->getOrders();
foreach ($xml->OrderArray->Order as $orderXml) {
    $order = Order::fromXml($orderXml);
    
    echo $order->orderId;
    echo $order->total;
    echo $order->orderStatus->title();
}

// Convert to array
$array = $order->toArray();
```

## Error Handling

```php
use Tigusigalpa\Ebay\Exceptions\{EbayApiException, AuthenticationException, InvalidConfigurationException};

try {
    $orders = Ebay::trading()->getOrders();
} catch (AuthenticationException $e) {
    Log::error('eBay auth failed', [
        'error_code' => $e->getErrorCode(),
        'message' => $e->getMessage(),
    ]);
} catch (EbayApiException $e) {
    foreach ($e->getErrors() as $error) {
        echo $error['code'] . ': ' . $error['message'];
    }
} catch (InvalidConfigurationException $e) {
    Log::error('Invalid eBay configuration', ['message' => $e->getMessage()]);
}
```

## Optimization

### Caching

```php
// config/ebay.php
'cache' => [
    'enabled' => true,
    'ttl' => 3600,
],
```

### Rate Limiting

```php
RateLimiter::attempt('ebay-api', $perMinute = 5000, function() {
    // API calls
});
```

### Queues

```php
dispatch(new SyncEbayOrdersJob($dateRange));
dispatch(new UpdateInventoryJob($products));
```

### Batch Processing

```php
$itemIds = ['123', '456', '789'];
foreach ($itemIds as $itemId) {
    $items[] = Ebay::trading()->getItem($itemId);
    usleep(100000); // Rate limiting
}
```

## Testing

```bash
composer test

# Unit tests only
./vendor/bin/phpunit tests/Unit

# Feature tests only
./vendor/bin/phpunit tests/Feature
```

## FAQ

**How to get API credentials?**

Register at [eBay Developers Program](https://developer.ebay.com/), create application and get App ID, Cert ID, Dev ID, RuName.

**Can I use for dropshipping?**

Yes. Package is suitable for order automation, inventory, and listings.

**What's the difference between Trading API and Commerce API?**

- **Trading API (XML)** — legacy API for core operations (listings, orders, categories)
- **Commerce API (REST)** — modern REST API for inventory, fulfillment, and new features

**How to switch to production?**

Set `EBAY_ENVIRONMENT=production` in `.env` and add production credentials.

**How to handle rate limits?**

Use Laravel Rate Limiter or add `usleep()` between requests.

## Troubleshooting

**"Missing required configuration"**

Check that all credentials are set in `.env`:
```env
EBAY_SANDBOX_APP_ID=your-app-id
EBAY_SANDBOX_CERT_ID=your-cert-id
EBAY_SANDBOX_DEV_ID=your-dev-id
EBAY_SANDBOX_RUNAME=your-runame
```

**OAuth token expired**

Package refreshes tokens automatically. Ensure refresh token is set:
```php
Ebay::setRefreshToken($refreshToken, $expiresAt);
```

**XML parsing errors**

Enable logging in `config/ebay.php`:
```php
'logging' => ['enabled' => true],
```

## Support

- **GitHub Issues**: [tigusigalpa/ebay-php/issues](https://github.com/tigusigalpa/ebay-php/issues)
- **Discussions**: [GitHub Discussions](https://github.com/tigusigalpa/ebay-php/discussions)
- **Email**: sovletig@gmail.com

## eBay Documentation

- [Trading API Reference](https://developer.ebay.com/devzone/xml/docs/Reference/ebay/index.html)
- [Commerce API Reference](https://developer.ebay.com/api-docs/commerce/static/overview.html)
- [OAuth 2.0 Guide](https://developer.ebay.com/api-docs/static/oauth-tokens.html)
- [Error Messages](https://developer.ebay.com/devzone/xml/docs/Reference/ebay/Errors/errormessages.htm)
- [API Rate Limits](https://developer.ebay.com/support/app-check)

## Contributing

```bash
git clone https://github.com/tigusigalpa/ebay-php.git
cd ebay-php
composer install
composer test
composer check-style
```

Requirements:
- PSR-12
- Tests for new features
- PHPDoc with links to eBay API docs
- Strict typing

## License

MIT. See [LICENSE](LICENSE).

## Author

**Igor Sazonov**
- GitHub: [@tigusigalpa](https://github.com/tigusigalpa)
- Email: sovletig@gmail.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
