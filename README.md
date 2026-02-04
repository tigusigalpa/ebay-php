# eBay PHP/Laravel Package

[![Latest Version](https://img.shields.io/packagist/v/tigusigalpa/ebay-php.svg)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![License](https://img.shields.io/packagist/l/tigusigalpa/ebay-php.svg)](https://packagist.org/packages/tigusigalpa/ebay-php)

Modern, well-documented PHP/Laravel package for eBay API integration with OAuth 2.0 support. Built with PHP 8.1+ features including native Enums, strict typing, and following SOLID principles and PSR standards.

## Features

- ✅ **PHP 8.1+** with strict typing and native Enums
- ✅ **OAuth 2.0** authentication with automatic token refresh
- ✅ **Trading API** (XML) support
- ✅ **Commerce API** (REST) support
- ✅ **Multiple eBay sites** support (US, UK, DE, FR, etc.)
- ✅ **Fluent interface** for easy API interactions
- ✅ **Type-safe DTOs** for API responses
- ✅ **Comprehensive documentation** with links to official eBay docs
- ✅ **PSR-4, PSR-7, PSR-12** compliant
- ✅ **Laravel integration** with Service Provider and Facade

## Requirements

- PHP 8.1 or higher
- Laravel 9.x, 10.x, 11.x, or 12.x
- Guzzle 7.x

## Installation

Install the package via Composer:

```bash
composer require tigusigalpa/ebay-php
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=ebay-config
```

## Configuration

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

## Testing

```bash
composer test
```

## Documentation Links

This package includes extensive inline documentation with direct links to official eBay API documentation:

- [Trading API Reference](https://developer.ebay.com/devzone/xml/docs/Reference/ebay/index.html)
- [Commerce API Reference](https://developer.ebay.com/api-docs/commerce/static/overview.html)
- [OAuth 2.0 Guide](https://developer.ebay.com/api-docs/static/oauth-tokens.html)
- [Error Messages](https://developer.ebay.com/devzone/xml/docs/Reference/ebay/Errors/errormessages.htm)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- **Igor Sazonov** - Original library author and maintainer
- GitHub: [@tigusigalpa](https://github.com/tigusigalpa)
- Email: sovletig@gmail.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.
