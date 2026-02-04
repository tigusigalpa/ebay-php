# eBay API PHP/Laravel Package - Complete Integration Solution

![eBay PHP SDK](https://github.com/user-attachments/assets/629586b3-1a78-4919-98e0-f8fd3cef57e0)

[![Latest Version](https://img.shields.io/packagist/v/tigusigalpa/ebay-php.svg)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![License](https://img.shields.io/packagist/l/tigusigalpa/ebay-php.svg)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![PHP Version](https://img.shields.io/packagist/php-v/tigusigalpa/ebay-php.svg)](https://packagist.org/packages/tigusigalpa/ebay-php)

**The most comprehensive and modern PHP/Laravel package for eBay API integration.** Seamlessly integrate eBay Trading
API, Commerce API, and OAuth 2.0 authentication into your Laravel e-commerce application. Built with PHP 8.1+ features
including native Enums, strict typing, and following SOLID principles and PSR standards.

## Why Choose This eBay Package?

This eBay PHP SDK provides a **production-ready solution** for Laravel developers who need to integrate with eBay's
marketplace platform. Whether you're building an eBay seller tool, inventory management system, order fulfillment
application, or multi-channel e-commerce platform, this package offers everything you need.

### Perfect For:

- 🛒 **E-commerce platforms** integrating with eBay marketplace
- 📦 **Inventory management systems** syncing products with eBay
- 📊 **Order management tools** for eBay sellers
- 🔄 **Multi-channel selling applications**
- 🤖 **eBay automation tools** and dropshipping platforms
- 📈 **Analytics dashboards** for eBay sales data
- 🏪 **eBay store management** applications

## Key Features

### eBay API Coverage

- ✅ **eBay Trading API (XML)** - Complete support for GetOrders, GetCategories, GetItem, AddFixedPriceItem,
  GetMyEbaySelling, and more
- ✅ **eBay Commerce API (REST)** - Taxonomy, Translation, Inventory, and Fulfillment APIs
- ✅ **Finding API Ready** - Extensible architecture for easy Finding API integration
- ✅ **Multi-Marketplace Support** - US, UK, Germany, France, Australia, Canada, and 15+ eBay sites

### Authentication & Security

- ✅ **OAuth 2.0 Authentication** - Full implementation with Authorization Code, Client Credentials, and Refresh Token
  grants
- ✅ **Automatic Token Refresh** - Smart token management with automatic renewal before expiration
- ✅ **Secure Credential Storage** - Environment-based configuration with Laravel encryption support
- ✅ **Session Management** - Built-in support for multi-user eBay account connections

### Developer Experience

- ✅ **PHP 8.1+ Native Enums** - Type-safe enumerations for Sites, Currencies, Order Status, Payment Status, and more
- ✅ **Strict Type Safety** - Full type hints and return types throughout the codebase
- ✅ **Fluent Interface** - Chainable methods for intuitive API calls
- ✅ **Laravel Facade** - Easy static access via `Ebay::trading()->getOrders()`
- ✅ **Dependency Injection** - Full support for Laravel's service container
- ✅ **IDE Autocomplete** - Complete PHPDoc annotations for intelligent code completion

### Data Handling

- ✅ **Type-Safe DTOs** - Data Transfer Objects for Orders, Items, and other resources
- ✅ **XML & JSON Support** - Seamless handling of both Trading API (XML) and Commerce API (JSON)
- ✅ **Response Validation** - Automatic error detection and exception throwing
- ✅ **Data Transformation** - Easy conversion between XML, arrays, and objects

### Code Quality & Standards

- ✅ **SOLID Principles** - Clean architecture with single responsibility and dependency inversion
- ✅ **PSR-4 Autoloading** - Standard PHP autoloading
- ✅ **PSR-7 HTTP Messages** - Standard HTTP request/response handling
- ✅ **PSR-12 Code Style** - Consistent, readable code formatting
- ✅ **Comprehensive Testing** - PHPUnit test suite with unit and feature tests

### Production Ready

- ✅ **Error Handling** - Custom exception hierarchy with detailed error information
- ✅ **Logging Support** - Configurable request/response logging for debugging
- ✅ **Rate Limiting** - Built-in support for eBay API rate limits
- ✅ **Caching** - Optional response caching for improved performance
- ✅ **Environment Switching** - Easy toggle between sandbox and production

### Documentation

- ✅ **Extensive README** - Complete usage examples and best practices
- ✅ **Inline Documentation** - Every method includes links to official eBay API docs
- ✅ **Code Examples** - Real-world usage scenarios and patterns
- ✅ **Migration Guide** - Easy upgrade path from other eBay packages

## Requirements

- PHP 8.1 or higher
- Laravel 9.x, 10.x, 11.x, or 12.x
- Guzzle 7.x

## Installation Guide

### Step 1: Install via Composer

Install the eBay API package into your Laravel application using Composer:

```bash
composer require tigusigalpa/ebay-php
```

The package will automatically register its service provider and facade through Laravel's package auto-discovery.

### Step 2: Publish Configuration

Publish the eBay configuration file to your Laravel application:

```bash
php artisan vendor:publish --tag=ebay-config
```

This creates `config/ebay.php` where you can customize all eBay API settings.

### Step 3: Get eBay API Credentials

Before using this package, you need to obtain eBay API credentials:

1. Visit [eBay Developers Program](https://developer.ebay.com/)
2. Sign up or log in to your eBay developer account
3. Create a new application (Keyset)
4. Get your **App ID (Client ID)**, **Cert ID (Client Secret)**, **Dev ID**, and **RuName (Redirect URL)**
5. Configure OAuth scopes for your application

### Step 4: Configure Environment Variables

Add your eBay API credentials to your `.env` file:

```env
# Environment: sandbox or production
EBAY_ENVIRONMENT=sandbox

# Sandbox Credentials
EBAY_SANDBOX_APP_ID=your-app-id
EBAY_SANDBOX_CERT_ID=your-cert-id
EBAY_SANDBOX_DEV_ID=your-dev-id
EBAY_SANDBOX_RUNAME=your-runame

# Production Credentials
EBAY_PRODUCTION_APP_ID=your-production-app-id
EBAY_PRODUCTION_CERT_ID=your-production-cert-id
EBAY_PRODUCTION_DEV_ID=your-production-dev-id
EBAY_PRODUCTION_RUNAME=your-production-runame

# Default Site
EBAY_DEFAULT_SITE=US
```

## Real-World Use Cases

### 1. eBay Order Management System

Automatically fetch and process eBay orders in your Laravel application:

```php
// Sync eBay orders to your database
$orders = Ebay::trading()->getOrders([
    'CreateTimeFrom' => now()->subHours(1)->toIso8601String(),
    'OrderStatus' => 'Active',
]);
```

### 2. Multi-Channel Inventory Sync

Keep your inventory synchronized across eBay and other platforms:

```php
// Update eBay inventory when stock changes
Ebay::commerce()->createOrReplaceInventoryItem($sku, [
    'availability' => ['shipToLocationAvailability' => ['quantity' => $newQuantity]],
]);
```

### 3. Automated Product Listing

Bulk upload products to eBay from your Laravel database:

```php
// Create eBay listings from your products
foreach ($products as $product) {
    Ebay::trading()->addFixedPriceItem([
        'Title' => $product->name,
        'StartPrice' => $product->price,
        // ... more fields
    ]);
}
```

### 4. International Marketplace Expansion

Easily list products on multiple eBay marketplaces:

```php
// List on UK, Germany, and France eBay sites
foreach ([Site::UK, Site::GERMANY, Site::FRANCE] as $site) {
    Ebay::setSite($site)->trading()->addFixedPriceItem($itemData);
}
```

### 5. eBay Analytics Dashboard

Build comprehensive sales analytics for eBay sellers:

```php
// Fetch sales data for analytics
$orders = Ebay::trading()->getOrders([
    'CreateTimeFrom' => now()->subMonth()->toIso8601String(),
]);
// Process and display in your dashboard
```

## Quick Start

### Using the Facade

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Tigusigalpa\Ebay\Enums\Site;

// Get OAuth consent URL
$consentUrl = Ebay::getConsentUrl();

// Exchange authorization code for token
$tokenData = Ebay::exchangeCodeForToken($code);

// Set site
Ebay::setSite(Site::UK);

// Get orders via Trading API
$orders = Ebay::trading()->getOrders([
    'CreateTimeFrom' => '2024-01-01T00:00:00.000Z',
    'CreateTimeTo' => '2024-12-31T23:59:59.999Z',
]);

// Get item details
$item = Ebay::trading()->getItem('123456789');

// Translate text via Commerce API
$translated = Ebay::commerce()->translate(
    'Hello World',
    'en',
    'de',
    'ITEM_TITLE'
);
```

### Using Dependency Injection

```php
use Tigusigalpa\Ebay\Ebay;
use Tigusigalpa\Ebay\Enums\Site;

class OrderController extends Controller
{
    public function __construct(protected Ebay $ebay)
    {
    }

    public function index()
    {
        $this->ebay->setSite(Site::US);
        
        $orders = $this->ebay->trading()->getOrders([
            'OrderStatus' => 'Active',
        ]);
        
        // Process orders...
    }
}
```

## Authentication

### OAuth 2.0 Flow

#### 1. Get Consent URL

```php
use Tigusigalpa\Ebay\Facades\Ebay;

$consentUrl = Ebay::getConsentUrl(
    scopes: config('ebay.scopes'),
    state: 'your-state-parameter',
    locale: 'en-US'
);

return redirect($consentUrl);
```

#### 2. Handle Callback

```php
public function callback(Request $request)
{
    $code = $request->get('code');
    
    $tokenData = Ebay::exchangeCodeForToken($code);
    
    // Store tokens in database
    auth()->user()->update([
        'ebay_access_token' => $tokenData['access_token'],
        'ebay_access_token_expires_at' => $tokenData['expires_at'],
        'ebay_refresh_token' => $tokenData['refresh_token'],
        'ebay_refresh_token_expires_at' => $tokenData['refresh_token_expires_at'],
    ]);
    
    return redirect()->route('dashboard');
}
```

#### 3. Use Stored Tokens

```php
$user = auth()->user();

Ebay::setAccessToken(
    $user->ebay_access_token,
    $user->ebay_access_token_expires_at
);

Ebay::setRefreshToken(
    $user->ebay_refresh_token,
    $user->ebay_refresh_token_expires_at
);

// Tokens will be automatically refreshed if expired
$orders = Ebay::trading()->getOrders();
```

## Trading API Examples

### Get Orders

```php
use Tigusigalpa\Ebay\Facades\Ebay;

$xml = Ebay::trading()->getOrders([
    'CreateTimeFrom' => '2024-01-01T00:00:00.000Z',
    'CreateTimeTo' => '2024-12-31T23:59:59.999Z',
    'OrderStatus' => 'Active',
]);

// Process XML response
foreach ($xml->OrderArray->Order as $order) {
    $orderId = (string) $order->OrderID;
    $total = (float) $order->Total;
    // ...
}
```

### Get Categories

```php
$categories = Ebay::trading()->getCategories([
    'CategorySiteID' => 0,
    'LevelLimit' => 2,
]);
```

### Get Item Details

```php
$item = Ebay::trading()->getItem('123456789');

$title = (string) $item->Item->Title;
$price = (float) $item->Item->SellingStatus->CurrentPrice;
```

### Add Fixed Price Item

```php
$response = Ebay::trading()->addFixedPriceItem([
    'Title' => 'My Product Title',
    'Description' => 'Product description',
    'PrimaryCategory' => ['CategoryID' => '12345'],
    'StartPrice' => 99.99,
    'Quantity' => 10,
    'Currency' => 'USD',
    'Country' => 'US',
    'Location' => 'New York',
    'PaymentMethods' => 'PayPal',
    'PayPalEmailAddress' => 'seller@example.com',
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

## Commerce API Examples

### Get Item Aspects for Category

```php
$aspects = Ebay::commerce()->getItemAspectsForCategory('0', '12345');

foreach ($aspects['aspects'] as $aspect) {
    echo $aspect['localizedAspectName'];
}
```

### Translate Text

```php
$translated = Ebay::commerce()->translate(
    text: 'Hello World',
    fromLanguage: 'en',
    toLanguage: 'de',
    context: 'ITEM_TITLE'
);

echo $translated; // "Hallo Welt"
```

### Get Inventory Item

```php
$item = Ebay::commerce()->getInventoryItem('SKU-123');
```

### Get Fulfillment Orders

```php
$orders = Ebay::commerce()->getFulfillmentOrders([
    'filter' => 'orderfulfillmentstatus:{NOT_STARTED|IN_PROGRESS}',
    'limit' => 50,
]);
```

## Working with Sites

```php
use Tigusigalpa\Ebay\Enums\Site;

// Set site by enum
Ebay::setSite(Site::UK);
Ebay::setSite(Site::GERMANY);
Ebay::setSite(Site::AUSTRALIA);

// Get site information
$site = Site::US;
echo $site->title();        // "United States"
echo $site->code();         // "us"
echo $site->url();          // "https://ebay.com"
echo $site->locale();       // "en-US"
echo $site->marketplace();  // "EBAY_US"
echo $site->currency()->symbol(); // "$"

// Find site by code
$site = Site::fromCode('uk');  // Returns Site::UK

// Find site by marketplace
$site = Site::fromMarketplace('EBAY_DE');  // Returns Site::GERMANY
```

## Working with Enums

The package uses PHP 8.1 native Enums for type safety:

```php
use Tigusigalpa\Ebay\Enums\{Site, Currency, ListingStatus, OrderStatus, PaymentStatus, ListingType};

// Currency
$currency = Currency::USD;
echo $currency->symbol();      // "$"
echo $currency->htmlEntity();  // "&#36;"
echo $currency->title();       // "US Dollar"

// Listing Status
$status = ListingStatus::ACTIVE;
echo $status->title();         // "Active"
echo $status->description();   // Full description...

// Order Status
$orderStatus = OrderStatus::COMPLETED;

// Payment Status
$paymentStatus = PaymentStatus::PAID;

// Listing Type
$listingType = ListingType::FIXED_PRICE;
echo $listingType->description(); // "Buy It Now format with a fixed price"
```

## DTOs (Data Transfer Objects)

The package provides type-safe DTOs for common responses:

```php
use Tigusigalpa\Ebay\Http\Resources\{Order, Item};

// From XML
$xml = Ebay::trading()->getOrders();
foreach ($xml->OrderArray->Order as $orderXml) {
    $order = Order::fromXml($orderXml);
    
    echo $order->orderId;
    echo $order->total;
    echo $order->orderStatus->title();
    echo $order->paymentStatus->value;
}

// From Array (REST API)
$orderData = Ebay::commerce()->getFulfillmentOrder('12345');
$order = Order::fromArray($orderData);

// Convert to array
$array = $order->toArray();
```

## Error Handling

```php
use Tigusigalpa\Ebay\Exceptions\{EbayApiException, AuthenticationException, InvalidConfigurationException};

try {
    $orders = Ebay::trading()->getOrders();
} catch (AuthenticationException $e) {
    // Handle authentication errors
    Log::error('eBay auth failed', [
        'error_code' => $e->getErrorCode(),
        'message' => $e->getMessage(),
    ]);
} catch (EbayApiException $e) {
    // Handle general API errors
    $errors = $e->getErrors();
    foreach ($errors as $error) {
        echo $error['code'] . ': ' . $error['message'];
    }
} catch (InvalidConfigurationException $e) {
    // Handle configuration errors
    Log::error('Invalid eBay configuration', ['message' => $e->getMessage()]);
}
```

## Advanced Usage

### Custom Compatibility Level

```php
Ebay::trading()->setCompatibilityLevel(1257);
```

### Switch Environment

```php
// Switch to production
Ebay::setEnvironment('production');

// Switch back to sandbox
Ebay::setEnvironment('sandbox');
```

### Get User Profile URL

```php
use Tigusigalpa\Ebay\Enums\Site;
use Tigusigalpa\Ebay\Facades\Ebay;

$url = Ebay::getUserUrl(Site::US, 'username123');
// Returns: https://ebay.com/usr/username123
```

## Frequently Asked Questions (FAQ)

### How do I get eBay API credentials?

Visit the [eBay Developers Program](https://developer.ebay.com/), create an account, and generate API keys for your
application. You'll need App ID, Cert ID, Dev ID, and RuName.

### Can I use this package for eBay dropshipping?

Yes! This package is perfect for dropshipping applications. You can automate order processing, inventory management, and
product listing across multiple eBay marketplaces.

### Does this support eBay Motors or eBay Classifieds?

The package supports all eBay marketplaces where the Trading and Commerce APIs are available. Check eBay's official
documentation for specific marketplace support.

### How do I handle eBay API rate limits?

The package includes built-in support for rate limiting. Use Laravel's rate limiter or implement custom throttling based
on your eBay API tier.

### Can I use this in production?

Absolutely! This package is production-ready with comprehensive error handling, logging, and token management. Just
switch `EBAY_ENVIRONMENT` to `production`.

### What's the difference between Trading API and Commerce API?

- **Trading API (XML)**: Legacy API for core eBay operations (listings, orders, categories)
- **Commerce API (REST)**: Modern REST API for inventory, fulfillment, and advanced features

### How do I migrate from another eBay package?

This package uses modern PHP 8.1+ features and follows Laravel conventions. Check the migration guide in EXAMPLES.md for
detailed instructions.

### Does this work with eBay Partner Network?

This package focuses on seller APIs. For eBay Partner Network (affiliate program), you'll need additional integration.

## Troubleshooting

### Common Issues and Solutions

**Issue: "Missing required configuration" error**

```php
// Solution: Ensure all credentials are set in .env
EBAY_SANDBOX_APP_ID=your-app-id
EBAY_SANDBOX_CERT_ID=your-cert-id
EBAY_SANDBOX_DEV_ID=your-dev-id
EBAY_SANDBOX_RUNAME=your-runame
```

**Issue: OAuth token expired**

```php
// Solution: The package auto-refreshes tokens, but ensure refresh token is set
Ebay::setRefreshToken($refreshToken, $expiresAt);
```

**Issue: XML parsing errors**

```php
// Solution: Enable logging to see raw responses
// In config/ebay.php:
'logging' => ['enabled' => true],
```

**Issue: Rate limit exceeded**

```php
// Solution: Implement rate limiting
RateLimiter::attempt('ebay-api', $perMinute = 5000, function() {
    // Your API calls
});
```

## Performance Optimization

### Caching eBay API Responses

Enable caching for frequently accessed data:

```php
// In config/ebay.php
'cache' => [
    'enabled' => true,
    'ttl' => 3600, // 1 hour
],
```

### Batch Processing

Process multiple items efficiently:

```php
// Batch get item details
$itemIds = ['123', '456', '789'];
foreach ($itemIds as $itemId) {
    $items[] = Ebay::trading()->getItem($itemId);
    usleep(100000); // Rate limiting
}
```

### Queue Integration

Use Laravel queues for long-running operations:

```php
// Dispatch eBay sync job
dispatch(new SyncEbayOrdersJob($dateRange));
```

## Testing

Run the test suite:

```bash
composer test
```

Run specific test types:

```bash
# Unit tests only
./vendor/bin/phpunit tests/Unit

# Feature tests only
./vendor/bin/phpunit tests/Feature
```

## Package Comparison

Why choose this eBay package over alternatives?

| Feature             | This Package                  | Other Packages   |
|---------------------|-------------------------------|------------------|
| PHP Version         | 8.1+ (Modern)                 | 7.x (Legacy)     |
| Type Safety         | ✅ Strict typing               | ❌ Minimal        |
| Enums               | ✅ Native PHP 8.1              | ❌ Static classes |
| OAuth 2.0           | ✅ Full support + auto-refresh | ⚠️ Basic         |
| Laravel Integration | ✅ Service Provider + Facade   | ⚠️ Manual setup  |
| Trading API         | ✅ Complete                    | ⚠️ Partial       |
| Commerce API        | ✅ REST support                | ❌ Not included   |
| Multi-Marketplace   | ✅ 15+ sites                   | ⚠️ Limited       |
| Error Handling      | ✅ Custom exceptions           | ❌ Basic          |
| Documentation       | ✅ Extensive + Examples        | ⚠️ Minimal       |
| Testing             | ✅ PHPUnit suite               | ❌ None           |
| Active Maintenance  | ✅ Yes                         | ❌ Abandoned      |

## Keywords & Tags

**eBay API**, **Laravel eBay**, **PHP eBay SDK**, **eBay Trading API**, **eBay Commerce API**, **eBay OAuth**, **eBay
Integration**, **eBay Marketplace**, **E-commerce Laravel**, **Multi-channel selling**, **eBay Seller Tools**, *
*Inventory Management**, **Order Management**, **eBay Automation**, **Dropshipping**, **eBay PHP Package**, **Laravel
Package**, **PHP 8.1**, **REST API**, **XML API**

## Documentation Links

This package includes extensive inline documentation with direct links to official eBay API documentation:

- [Trading API Reference](https://developer.ebay.com/devzone/xml/docs/Reference/ebay/index.html)
- [Commerce API Reference](https://developer.ebay.com/api-docs/commerce/static/overview.html)
- [OAuth 2.0 Guide](https://developer.ebay.com/api-docs/static/oauth-tokens.html)
- [Error Messages](https://developer.ebay.com/devzone/xml/docs/Reference/ebay/Errors/errormessages.htm)
- [eBay Developer Program](https://developer.ebay.com/)
- [API Rate Limits](https://developer.ebay.com/support/app-check)
- [Sandbox Testing](https://developer.ebay.com/api-docs/static/gs_create-a-developer-account.html)

## Support & Community

### Getting Help

- 📖 **Documentation**: Read the comprehensive [README](README.md) and [EXAMPLES](EXAMPLES.md)
- 🐛 **Bug Reports**: [Open an issue](https://github.com/tigusigalpa/ebay-php/issues) on GitHub
- 💡 **Feature Requests**: [Submit a feature request](https://github.com/tigusigalpa/ebay-php/issues/new)
- 📧 **Email Support**: sovletig@gmail.com
- 💬 **Discussions**: Join our [GitHub Discussions](https://github.com/tigusigalpa/ebay-php/discussions)

### Professional Support

Need help integrating eBay into your Laravel application? Professional support and custom development services are
available. Contact us for:

- Custom eBay integration development
- Migration from other eBay packages
- Performance optimization
- Training and consultation
- Enterprise support contracts

## Contributing

We welcome contributions from the community! Here's how you can help:

### Ways to Contribute

- 🐛 **Report Bugs**: Found a bug? [Open an issue](https://github.com/tigusigalpa/ebay-php/issues)
- ✨ **Suggest Features**: Have an idea? Share it in [Discussions](https://github.com/tigusigalpa/ebay-php/discussions)
- 📝 **Improve Documentation**: Help us make the docs better
- 🔧 **Submit Pull Requests**: Fix bugs or add features
- ⭐ **Star the Repository**: Show your support!

### Development Setup

```bash
# Clone the repository
git clone https://github.com/tigusigalpa/ebay-php.git
cd ebay-php

# Install dependencies
composer install

# Run tests
composer test

# Check code style
composer check-style
```

### Coding Standards

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation
- Use strict typing
- Add PHPDoc comments with eBay API links

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- **Igor Sazonov** - Original library author and maintainer
- GitHub: [@tigusigalpa](https://github.com/tigusigalpa)
- Email: sovletig@gmail.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.
