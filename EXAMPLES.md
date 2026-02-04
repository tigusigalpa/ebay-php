# eBay PHP Package - Usage Examples

This document provides comprehensive examples for using the eBay PHP/Laravel package.

## Table of Contents

- [Setup](#setup)
- [Authentication](#authentication)
- [Trading API Examples](#trading-api-examples)
- [Commerce API Examples](#commerce-api-examples)
- [Working with Enums](#working-with-enums)
- [Error Handling](#error-handling)
- [Advanced Usage](#advanced-usage)

## Setup

### Installation

```bash
composer require tigusigalpa/ebay-php
php artisan vendor:publish --tag=ebay-config
```

### Environment Configuration

```env
EBAY_ENVIRONMENT=sandbox
EBAY_SANDBOX_APP_ID=YourAppId
EBAY_SANDBOX_CERT_ID=YourCertId
EBAY_SANDBOX_DEV_ID=YourDevId
EBAY_SANDBOX_RUNAME=YourRuName
```

## Authentication

### Complete OAuth 2.0 Flow

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Illuminate\Http\Request;

class EbayAuthController extends Controller
{
    // Step 1: Redirect user to eBay consent page
    public function redirect()
    {
        $consentUrl = Ebay::getConsentUrl(
            scopes: config('ebay.scopes'),
            state: csrf_token(),
            locale: 'en-US'
        );
        
        return redirect($consentUrl);
    }
    
    // Step 2: Handle callback from eBay
    public function callback(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');
        
        // Verify state to prevent CSRF
        if ($state !== csrf_token()) {
            abort(403, 'Invalid state parameter');
        }
        
        try {
            // Exchange code for tokens
            $tokenData = Ebay::exchangeCodeForToken($code);
            
            // Store tokens in database
            auth()->user()->update([
                'ebay_access_token' => $tokenData['access_token'],
                'ebay_access_token_expires_at' => $tokenData['expires_at'],
                'ebay_refresh_token' => $tokenData['refresh_token'],
                'ebay_refresh_token_expires_at' => $tokenData['refresh_token_expires_at'],
            ]);
            
            return redirect()->route('dashboard')
                ->with('success', 'eBay account connected successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('settings')
                ->with('error', 'Failed to connect eBay account: ' . $e->getMessage());
        }
    }
    
    // Step 3: Use stored tokens
    public function useStoredTokens()
    {
        $user = auth()->user();
        
        Ebay::setAccessToken(
            $user->ebay_access_token,
            $user->ebay_access_token_expires_at
        );
        
        Ebay::setRefreshToken(
            $user->ebay_refresh_token,
            $user->ebay_refresh_token_expires_at
        );
        
        // Now you can make authenticated API calls
        $orders = Ebay::trading()->getOrders();
    }
}
```

### Application Token (No User Authorization)

```php
use Tigusigalpa\Ebay\Facades\Ebay;

// Get application token for public API calls
$tokenData = Ebay::auth()->getApplicationToken();

Ebay::setAccessToken(
    $tokenData['access_token'],
    $tokenData['expires_at']
);

// Now you can make public API calls
$categories = Ebay::trading()->getCategories();
```

## Trading API Examples

### Get Orders with Filters

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Tigusigalpa\Ebay\Http\Resources\Order;

// Get orders from the last 30 days
$xml = Ebay::trading()->getOrders([
    'CreateTimeFrom' => now()->subDays(30)->toIso8601String(),
    'CreateTimeTo' => now()->toIso8601String(),
    'OrderStatus' => 'Active',
    'OrderRole' => 'Seller',
]);

// Process orders
$orders = [];
if (isset($xml->OrderArray->Order)) {
    foreach ($xml->OrderArray->Order as $orderXml) {
        $order = Order::fromXml($orderXml);
        $orders[] = $order;
        
        echo "Order ID: {$order->orderId}\n";
        echo "Total: {$order->currencyCode} {$order->total}\n";
        echo "Status: {$order->orderStatus->title()}\n";
        echo "Buyer: {$order->buyerUserId}\n";
        echo "---\n";
    }
}
```

### Get Active Listings

```php
use Tigusigalpa\Ebay\Facades\Ebay;

$xml = Ebay::trading()->getMyEbaySelling([
    'ActiveList' => [
        'Include' => true,
        'Pagination' => [
            'EntriesPerPage' => 50,
            'PageNumber' => 1,
        ],
    ],
]);

if (isset($xml->ActiveList->ItemArray->Item)) {
    foreach ($xml->ActiveList->ItemArray->Item as $item) {
        $itemId = (string) $item->ItemID;
        $title = (string) $item->Title;
        $price = (float) $item->SellingStatus->CurrentPrice;
        $quantity = (int) $item->Quantity;
        
        echo "{$itemId}: {$title} - \${$price} ({$quantity} available)\n";
    }
}
```

### Create a Fixed Price Listing

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Tigusigalpa\Ebay\Enums\{Site, Condition, Country};

Ebay::setSite(Site::US);

$itemData = [
    'Title' => 'Brand New iPhone 15 Pro Max 256GB',
    'Description' => '<![CDATA[<h1>iPhone 15 Pro Max</h1><p>Brand new, sealed in box.</p>]]>',
    'PrimaryCategory' => [
        'CategoryID' => '9355',
    ],
    'StartPrice' => 1199.99,
    'ConditionID' => Condition::NEW->value,
    'Country' => Country::US->value,
    'Currency' => 'USD',
    'DispatchTimeMax' => 3,
    'ListingDuration' => 'GTC',
    'ListingType' => 'FixedPriceItem',
    'Location' => 'New York, NY',
    'Quantity' => 5,
    'PaymentMethods' => 'PayPal',
    'PayPalEmailAddress' => 'seller@example.com',
    'PictureDetails' => [
        'PictureURL' => [
            'https://example.com/image1.jpg',
            'https://example.com/image2.jpg',
        ],
    ],
    'ShippingDetails' => [
        'ShippingType' => 'Flat',
        'ShippingServiceOptions' => [
            'ShippingServicePriority' => 1,
            'ShippingService' => 'USPSPriority',
            'ShippingServiceCost' => 9.99,
        ],
    ],
    'ReturnPolicy' => [
        'ReturnsAcceptedOption' => 'ReturnsAccepted',
        'RefundOption' => 'MoneyBack',
        'ReturnsWithinOption' => 'Days_30',
        'ShippingCostPaidByOption' => 'Buyer',
    ],
];

try {
    $response = Ebay::trading()->addFixedPriceItem($itemData);
    
    $itemId = (string) $response->ItemID;
    $fees = [];
    
    if (isset($response->Fees->Fee)) {
        foreach ($response->Fees->Fee as $fee) {
            $fees[] = [
                'name' => (string) $fee->Name,
                'amount' => (float) $fee->Fee,
            ];
        }
    }
    
    echo "Item created successfully!\n";
    echo "Item ID: {$itemId}\n";
    echo "Fees:\n";
    foreach ($fees as $fee) {
        echo "  {$fee['name']}: \${$fee['amount']}\n";
    }
    
} catch (\Exception $e) {
    echo "Error creating listing: " . $e->getMessage();
}
```

### Get Category Details

```php
use Tigusigalpa\Ebay\Facades\Ebay;

// Get top-level categories
$xml = Ebay::trading()->getCategories([
    'CategorySiteID' => 0,
    'LevelLimit' => 1,
    'ViewAllNodes' => false,
]);

$categories = [];
if (isset($xml->CategoryArray->Category)) {
    foreach ($xml->CategoryArray->Category as $category) {
        $categories[] = [
            'id' => (string) $category->CategoryID,
            'name' => (string) $category->CategoryName,
            'level' => (int) $category->CategoryLevel,
        ];
    }
}

print_r($categories);
```

### Get Item Specifics for Category

```php
use Tigusigalpa\Ebay\Facades\Ebay;

$xml = Ebay::trading()->getCategoryFeatures('9355');

if (isset($xml->Category->ItemSpecifics->ItemSpecific)) {
    foreach ($xml->Category->ItemSpecifics->ItemSpecific as $specific) {
        $name = (string) $specific->Name;
        $required = (string) ($specific->MinValues ?? '0') > 0;
        
        echo "{$name}" . ($required ? ' (Required)' : '') . "\n";
        
        if (isset($specific->ValueList->Value)) {
            foreach ($specific->ValueList->Value as $value) {
                echo "  - {$value}\n";
            }
        }
    }
}
```

## Commerce API Examples

### Translate Product Titles

```php
use Tigusigalpa\Ebay\Facades\Ebay;

$translations = [
    'en' => 'Brand New iPhone 15 Pro Max',
];

$languages = ['de', 'fr', 'es', 'it'];

foreach ($languages as $lang) {
    $translated = Ebay::commerce()->translate(
        text: $translations['en'],
        fromLanguage: 'en',
        toLanguage: $lang,
        context: 'ITEM_TITLE'
    );
    
    if ($translated) {
        $translations[$lang] = $translated;
        echo "{$lang}: {$translated}\n";
    }
}
```

### Get Inventory Items

```php
use Tigusigalpa\Ebay\Facades\Ebay;

try {
    $item = Ebay::commerce()->getInventoryItem('SKU-12345');
    
    echo "SKU: {$item['sku']}\n";
    echo "Title: {$item['product']['title']}\n";
    echo "Price: {$item['availability']['shipToLocationAvailability']['quantity']}\n";
    
} catch (\Exception $e) {
    echo "Item not found: " . $e->getMessage();
}
```

### Create/Update Inventory Item

```php
use Tigusigalpa\Ebay\Facades\Ebay;

$itemData = [
    'availability' => [
        'shipToLocationAvailability' => [
            'quantity' => 10,
        ],
    ],
    'condition' => 'NEW',
    'product' => [
        'title' => 'Brand New iPhone 15 Pro Max 256GB',
        'description' => 'Brand new, sealed in original packaging.',
        'aspects' => [
            'Brand' => ['Apple'],
            'Model' => ['iPhone 15 Pro Max'],
            'Storage Capacity' => ['256 GB'],
            'Color' => ['Natural Titanium'],
        ],
        'imageUrls' => [
            'https://example.com/image1.jpg',
            'https://example.com/image2.jpg',
        ],
    ],
];

$result = Ebay::commerce()->createOrReplaceInventoryItem('SKU-12345', $itemData);
```

### Get Fulfillment Orders

```php
use Tigusigalpa\Ebay\Facades\Ebay;

$orders = Ebay::commerce()->getFulfillmentOrders([
    'filter' => 'orderfulfillmentstatus:{NOT_STARTED|IN_PROGRESS}',
    'limit' => 50,
    'offset' => 0,
]);

if (isset($orders['orders'])) {
    foreach ($orders['orders'] as $order) {
        echo "Order ID: {$order['orderId']}\n";
        echo "Buyer: {$order['buyer']['username']}\n";
        echo "Total: {$order['pricingSummary']['total']['value']} {$order['pricingSummary']['total']['currency']}\n";
        echo "Status: {$order['orderFulfillmentStatus']}\n";
        echo "---\n";
    }
}
```

## Working with Enums

### Site Selection

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Tigusigalpa\Ebay\Enums\Site;

// Set site for UK marketplace
Ebay::setSite(Site::UK);

// Get site information
$site = Ebay::getSite();
echo "Trading on: {$site->title()}\n";
echo "URL: {$site->url()}\n";
echo "Currency: {$site->currency()->symbol()}\n";
echo "Locale: {$site->locale()}\n";

// Find site by code
$germanySite = Site::fromCode('de');
Ebay::setSite($germanySite);

// Find site by marketplace ID
$australiaSite = Site::fromMarketplace('EBAY_AU');
Ebay::setSite($australiaSite);
```

### Working with Currencies

```php
use Tigusigalpa\Ebay\Enums\Currency;

$currency = Currency::EUR;

echo "Symbol: {$currency->symbol()}\n";           // €
echo "HTML: {$currency->htmlEntity()}\n";         // &#8364;
echo "Title: {$currency->title()}\n";             // Euro
echo "Code: {$currency->value}\n";                // EUR

// Format price
$price = 99.99;
echo "{$currency->symbol()}{$price}\n";           // €99.99
```

### Item Conditions

```php
use Tigusigalpa\Ebay\Enums\Condition;

foreach (Condition::cases() as $condition) {
    echo "{$condition->value}: {$condition->title()}\n";
    echo "  {$condition->description()}\n\n";
}

// Use in listing
$itemCondition = Condition::MANUFACTURER_REFURBISHED;
$itemData = [
    'ConditionID' => $itemCondition->value,
    // ... other fields
];
```

## Error Handling

### Comprehensive Error Handling

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Tigusigalpa\Ebay\Exceptions\{
    EbayApiException,
    AuthenticationException,
    InvalidConfigurationException
};
use Illuminate\Support\Facades\Log;

try {
    $orders = Ebay::trading()->getOrders();
    
} catch (AuthenticationException $e) {
    // Token expired or invalid
    Log::error('eBay authentication failed', [
        'error_code' => $e->getErrorCode(),
        'message' => $e->getMessage(),
    ]);
    
    // Redirect to re-authenticate
    return redirect()->route('ebay.auth.redirect')
        ->with('error', 'Please reconnect your eBay account');
        
} catch (EbayApiException $e) {
    // API error (rate limit, invalid request, etc.)
    $errors = $e->getErrors();
    
    Log::error('eBay API error', [
        'error_code' => $e->getErrorCode(),
        'message' => $e->getMessage(),
        'errors' => $errors,
    ]);
    
    foreach ($errors as $error) {
        echo "Error {$error['code']}: {$error['message']}\n";
    }
    
} catch (InvalidConfigurationException $e) {
    // Configuration issue
    Log::critical('eBay configuration error', [
        'message' => $e->getMessage(),
    ]);
    
    throw $e; // Re-throw for admin attention
    
} catch (\Exception $e) {
    // Unexpected error
    Log::error('Unexpected eBay error', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}
```

## Advanced Usage

### Multi-Site Operations

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Tigusigalpa\Ebay\Enums\Site;

$sites = [Site::US, Site::UK, Site::GERMANY, Site::AUSTRALIA];
$allOrders = [];

foreach ($sites as $site) {
    Ebay::setSite($site);
    
    try {
        $xml = Ebay::trading()->getOrders([
            'CreateTimeFrom' => now()->subDays(7)->toIso8601String(),
        ]);
        
        if (isset($xml->OrderArray->Order)) {
            foreach ($xml->OrderArray->Order as $order) {
                $allOrders[] = [
                    'site' => $site->code(),
                    'order_id' => (string) $order->OrderID,
                    'total' => (float) $order->Total,
                    'currency' => (string) $order->Total['currencyID'],
                ];
            }
        }
    } catch (\Exception $e) {
        Log::warning("Failed to get orders from {$site->code()}: {$e->getMessage()}");
    }
}

echo "Total orders across all sites: " . count($allOrders) . "\n";
```

### Batch Processing with Rate Limiting

```php
use Tigusigalpa\Ebay\Facades\Ebay;
use Illuminate\Support\Facades\RateLimiter;

$itemIds = ['123456', '234567', '345678', /* ... */];

foreach ($itemIds as $itemId) {
    RateLimiter::attempt(
        'ebay-api',
        $perMinute = 5000,
        function() use ($itemId) {
            try {
                $item = Ebay::trading()->getItem($itemId);
                // Process item...
            } catch (\Exception $e) {
                Log::error("Failed to get item {$itemId}: {$e->getMessage()}");
            }
        }
    );
    
    // Small delay to avoid rate limits
    usleep(100000); // 0.1 seconds
}
```

### Custom Compatibility Level

```php
use Tigusigalpa\Ebay\Facades\Ebay;

// Use a specific API version
Ebay::trading()->setCompatibilityLevel(1257);

$orders = Ebay::trading()->getOrders();
```

### Environment Switching

```php
use Tigusigalpa\Ebay\Facades\Ebay;

// Test in sandbox
Ebay::setEnvironment('sandbox');
$testOrders = Ebay::trading()->getOrders();

// Switch to production
Ebay::setEnvironment('production');
$realOrders = Ebay::trading()->getOrders();
```

## Best Practices

1. **Always handle exceptions** - eBay API can fail for various reasons
2. **Store tokens securely** - Use encrypted database columns
3. **Implement token refresh** - Check expiration before API calls
4. **Respect rate limits** - Use Laravel's rate limiter
5. **Log API calls** - Enable logging in config for debugging
6. **Use DTOs** - Convert XML/JSON responses to type-safe objects
7. **Cache responses** - Cache category data and other static information
8. **Test in sandbox** - Always test new features in sandbox first

For more information, visit the [official eBay API documentation](https://developer.ebay.com/).
