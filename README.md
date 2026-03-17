# eBay PHP/Laravel SDK

![eBay PHP SDK](https://github.com/user-attachments/assets/629586b3-1a78-4919-98e0-f8fd3cef57e0)

[![Latest Version](https://img.shields.io/packagist/v/tigusigalpa/ebay-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![License](https://img.shields.io/packagist/l/tigusigalpa/ebay-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![PHP Version](https://img.shields.io/packagist/php-v/tigusigalpa/ebay-php.svg?style=flat-square)](https://packagist.org/packages/tigusigalpa/ebay-php)
[![Laravel](https://img.shields.io/badge/Laravel-9%2B-FF2D20.svg?style=flat-square&logo=Laravel)](https://laravel.com)
[![eBay](https://img.shields.io/badge/eBay-API-0064D2.svg?style=flat-square&logo=eBay)](https://developer.ebay.com)

PHP SDK for eBay API. Supports Trading API (XML), Commerce API (REST), Fulfillment API, Logistics API, OAuth 2.0 authentication, 20+ marketplaces. Works as standalone and with Laravel.

> 📖 **[Full documentation available on Wiki](https://github.com/tigusigalpa/ebay-php/wiki)**

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Authentication](#authentication)
- [Trading API](#trading-api)
- [Commerce API](#commerce-api)
- [Fulfillment API](#fulfillment-api)
- [Logistics API](#logistics-api)
- [Message API](#message-api)
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
- Commerce API (REST) — inventory, taxonomy, translation
- Fulfillment API (REST) — orders, shipping fulfillments, refunds, payment disputes
- Logistics API (REST) — shipping quotes, label generation, shipment tracking
- Message API (REST) — buyer-seller messaging, conversations, notifications
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

## Fulfillment API

The Fulfillment API v1 provides comprehensive order management, shipping fulfillment, refunds, and payment dispute handling.

### Get Orders

```php
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\{Order, OrderSearchPagedCollection};

// Get single order
$order = Ebay::fulfillment()->getOrder('12-34567-89012');
echo $order->orderId;
echo $order->orderFulfillmentStatus;
echo $order->pricingSummary?->total?->value;

// Get multiple orders with filters
$orders = Ebay::fulfillment()->getOrders([
    'filter' => 'creationdate:[2024-01-01T00:00:00.000Z..]',
    'limit' => 50,
    'offset' => 0,
]);

foreach ($orders->orders as $order) {
    echo $order->orderId;
    echo $order->buyer?->username;
}

// Filter by order status
$orders = Ebay::fulfillment()->getOrders([
    'filter' => 'orderfulfillmentstatus:{NOT_STARTED|IN_PROGRESS}',
]);

// Get specific orders by IDs
$orders = Ebay::fulfillment()->getOrders([
    'orderIds' => '12-34567-89012,12-34567-89013',
]);

// Include tax breakdown
$order = Ebay::fulfillment()->getOrder('12-34567-89012', [
    'fieldGroups' => 'TAX_BREAKDOWN',
]);
```

### Issue Refunds

```php
use Tigusigalpa\Ebay\Enums\ReasonForRefundEnum;

// Full refund
$refund = Ebay::fulfillment()->issueRefund('12-34567-89012', [
    'reasonForRefund' => ReasonForRefundEnum::BUYER_CANCEL->value,
    'comment' => 'Customer requested cancellation',
    'orderLevelRefundAmount' => [
        'value' => '99.99',
        'currency' => 'USD',
    ],
]);

// Partial refund for specific line items
$refund = Ebay::fulfillment()->issueRefund('12-34567-89012', [
    'reasonForRefund' => ReasonForRefundEnum::DEFECTIVE_ITEM->value,
    'refundItems' => [
        [
            'lineItemId' => '123456789',
            'refundAmount' => [
                'value' => '25.00',
                'currency' => 'USD',
            ],
        ],
    ],
]);

echo $refund->refundId;
echo $refund->refundStatus;
```

### Shipping Fulfillments

```php
// Create shipping fulfillment
$fulfillmentId = Ebay::fulfillment()->createShippingFulfillment('12-34567-89012', [
    'lineItems' => [
        [
            'lineItemId' => '123456789',
            'quantity' => 1,
        ],
    ],
    'shippedDate' => now()->toIso8601String(),
    'shippingCarrierCode' => 'USPS',
    'trackingNumber' => '1234567890123456',
]);

// Get single fulfillment
$fulfillment = Ebay::fulfillment()->getShippingFulfillment(
    '12-34567-89012',
    $fulfillmentId
);

echo $fulfillment->shipmentTrackingNumber;
echo $fulfillment->shippingCarrierCode;

// Get all fulfillments for order
$fulfillments = Ebay::fulfillment()->getShippingFulfillments('12-34567-89012');

foreach ($fulfillments->fulfillments as $fulfillment) {
    echo $fulfillment->trackingNumber;
}
```

### Payment Disputes

```php
use Tigusigalpa\Ebay\Enums\{DisputeStatusEnum, EvidenceTypeEnum};

// Get dispute summaries
$disputes = Ebay::fulfillment()->getPaymentDisputeSummaries([
    'payment_dispute_status' => DisputeStatusEnum::OPEN->value,
    'limit' => 50,
]);

foreach ($disputes->paymentDisputeSummaries as $summary) {
    echo $summary->paymentDisputeId;
    echo $summary->reason;
    echo $summary->amount?->value;
}

// Get full dispute details
$dispute = Ebay::fulfillment()->getPaymentDispute('5001234567890');

echo $dispute->paymentDisputeStatus?->value;
echo $dispute->respondByDate;
echo $dispute->revision; // Required for contesting

// Get dispute activity history
$history = Ebay::fulfillment()->getActivities('5001234567890');

foreach ($history->activities as $activity) {
    echo $activity->activityType;
    echo $activity->activityDate;
}

// Upload evidence file
$uploadResponse = Ebay::fulfillment()->uploadEvidenceFile(
    '5001234567890',
    file_get_contents('/path/to/tracking-proof.pdf')
);

$fileId = $uploadResponse->fileId;

// Add evidence
$evidenceResponse = Ebay::fulfillment()->addEvidence('5001234567890', [
    'evidenceType' => EvidenceTypeEnum::PROOF_OF_DELIVERY->value,
    'files' => [
        ['fileId' => $fileId],
    ],
    'lineItems' => [
        [
            'itemId' => '123456789',
            'lineItemId' => '987654321',
        ],
    ],
]);

$evidenceId = $evidenceResponse->evidenceId;

// Update evidence
Ebay::fulfillment()->updateEvidence('5001234567890', [
    'evidenceId' => $evidenceId,
    'evidenceType' => EvidenceTypeEnum::PROOF_OF_DELIVERY->value,
    'files' => [
        ['fileId' => $fileId],
    ],
]);

// Contest dispute
Ebay::fulfillment()->contestPaymentDispute('5001234567890', [
    'revision' => $dispute->revision,
    'returnAddress' => [
        'addressLine1' => '123 Main St',
        'city' => 'New York',
        'stateOrProvince' => 'NY',
        'postalCode' => '10001',
        'countryCode' => 'US',
        'fullName' => 'Your Company',
    ],
]);

// Accept dispute
Ebay::fulfillment()->acceptPaymentDispute('5001234567890');

// Download evidence file
$fileContent = Ebay::fulfillment()->fetchEvidenceContent(
    '5001234567890',
    $evidenceId,
    $fileId
);

file_put_contents('evidence.pdf', $fileContent);
```

### Filter Examples

```php
// Orders created in date range
$orders = Ebay::fulfillment()->getOrders([
    'filter' => 'creationdate:[2024-01-01T00:00:00.000Z..2024-12-31T23:59:59.999Z]',
]);

// Orders by fulfillment status
$orders = Ebay::fulfillment()->getOrders([
    'filter' => 'orderfulfillmentstatus:{NOT_STARTED}',
]);

// Disputes by buyer
$disputes = Ebay::fulfillment()->getPaymentDisputeSummaries([
    'buyer_username' => 'buyer123',
]);

// Disputes by order ID
$disputes = Ebay::fulfillment()->getPaymentDisputeSummaries([
    'order_id' => '12-34567-89012',
]);

// Disputes opened in date range
$disputes = Ebay::fulfillment()->getPaymentDisputeSummaries([
    'open_date_from' => '2024-01-01T00:00:00.000Z',
    'open_date_to' => '2024-12-31T23:59:59.999Z',
]);
```

### OAuth Scopes

Fulfillment API requires specific OAuth scopes:

- **Read operations**: `https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly`
- **Write operations**: `https://api.ebay.com/oauth/api_scope/sell.fulfillment`
- **Refunds**: `https://api.ebay.com/oauth/api_scope/sell.finances`
- **Payment disputes**: `https://api.ebay.com/oauth/api_scope/sell.payment.dispute`

Add to `config/ebay.php`:

```php
'scopes' => [
    'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly',
    'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
    'https://api.ebay.com/oauth/api_scope/sell.finances',
    'https://api.ebay.com/oauth/api_scope/sell.payment.dispute',
],
```

## Logistics API

The Logistics API v1_beta provides shipping quote generation, label creation, and shipment management through eBay's integrated shipping carriers.

### Create Shipping Quote

```php
use Tigusigalpa\Ebay\Http\Resources\Logistics\{ShippingQuote, Shipment};

// Create a shipping quote to get available rates
$quote = Ebay::logistics()->createShippingQuote([
    'orderId' => '12-12345-12345',
    'packageSpecification' => [
        'dimensions' => [
            'length' => '10',
            'width' => '10',
            'height' => '5',
            'unit' => 'INCH',
        ],
        'weight' => [
            'value' => '2',
            'unit' => 'POUND',
        ],
    ],
    'shipFrom' => [
        'fullName' => 'John Seller',
        'contactAddress' => [
            'addressLine1' => '123 Main St',
            'city' => 'San Jose',
            'stateOrProvince' => 'CA',
            'postalCode' => '95131',
            'countryCode' => 'US',
        ],
        'primaryPhone' => [
            'phoneNumber' => '555-1234',
        ],
        'email' => 'seller@example.com',
    ],
    'shipTo' => [
        'fullName' => 'Jane Buyer',
        'contactAddress' => [
            'addressLine1' => '456 Oak Ave',
            'city' => 'Austin',
            'stateOrProvince' => 'TX',
            'postalCode' => '78701',
            'countryCode' => 'US',
        ],
    ],
]);

echo $quote->shippingQuoteId;
echo $quote->expirationDate;

// Review available rates
foreach ($quote->rates ?? [] as $rate) {
    echo $rate->shippingServiceName;
    echo $rate->shippingCost?->value;
    echo $rate->shippingCost?->currency;
    echo $rate->minEstimatedDeliveryDate;
    echo $rate->maxEstimatedDeliveryDate;
    
    // Check rate recommendations
    if (in_array('CHEAPEST_RATE', $rate->rateRecommendation ?? [])) {
        echo "This is the cheapest option!";
    }
}
```

### Get Shipping Quote

```php
// Retrieve an existing shipping quote
$quote = Ebay::logistics()->getShippingQuote($shippingQuoteId);

echo $quote->shippingQuoteId;
echo $quote->orderId;

foreach ($quote->rates ?? [] as $rate) {
    echo $rate->rateId;
    echo $rate->carrierId;
    echo $rate->shippingCarrierCode;
}
```

### Create Shipment from Quote

```php
// Select a rate and create the shipment
$rateId = $quote->rates[0]->rateId; // Pick the first rate

$shipment = Ebay::logistics()->createFromShippingQuote([
    'shippingQuoteId' => $quote->shippingQuoteId,
    'rateId' => $rateId,
    'labelSize' => '4"x6"',
    'labelCustomMessage' => 'Thank you for your purchase!',
]);

echo $shipment->shipmentId;
echo $shipment->shipmentTrackingNumber;
echo $shipment->labelStatus; // OPEN, PURCHASED, EXPIRED, CANCELLED
echo $shipment->creationDate;
echo $shipment->labelExpirationDate;
```

### Download Shipping Label

```php
// Download label as PDF (default)
$labelPdf = Ebay::logistics()->downloadLabelFile($shipment->shipmentId);
file_put_contents('shipping-label.pdf', $labelPdf);

// Download label as ZPL (for thermal printers)
$labelZpl = Ebay::logistics()->downloadLabelFile(
    $shipment->shipmentId,
    'application/zpl'
);
file_put_contents('shipping-label.zpl', $labelZpl);
```

### Get Shipment Details

```php
// Retrieve shipment information
$shipment = Ebay::logistics()->getShipment($shipmentId);

echo $shipment->shipmentId;
echo $shipment->shipmentTrackingNumber;
echo $shipment->labelStatus;
echo $shipment->labelCustomMessage;

// Access rate details
echo $shipment->rate?->shippingServiceCode;
echo $shipment->rate?->shippingCost?->value;
echo $shipment->rate?->carrierId;

// Access addresses
echo $shipment->shipFrom?->fullName;
echo $shipment->shipFrom?->contactAddress?->city;
echo $shipment->shipTo?->fullName;
echo $shipment->returnTo?->contactAddress?->addressLine1;
```

### Cancel Shipment

```php
// Cancel a shipment before label expiration
Ebay::logistics()->cancelShipment($shipmentId);

// After cancellation, labelStatus changes to CANCELLED
$shipment = Ebay::logistics()->getShipment($shipmentId);
echo $shipment->labelStatus; // CANCELLED
```

### Complete Workflow Example

```php
// Step 1: Create shipping quote
$quote = Ebay::logistics()->createShippingQuote([
    'orderId' => '12-12345-12345',
    'packageSpecification' => [
        'dimensions' => ['length' => '10', 'width' => '10', 'height' => '5', 'unit' => 'INCH'],
        'weight' => ['value' => '2', 'unit' => 'POUND'],
    ],
    'shipFrom' => [
        'fullName' => 'Your Store',
        'contactAddress' => [
            'addressLine1' => '123 Warehouse Rd',
            'city' => 'Los Angeles',
            'stateOrProvince' => 'CA',
            'postalCode' => '90001',
            'countryCode' => 'US',
        ],
    ],
    'shipTo' => [
        'fullName' => 'Customer Name',
        'contactAddress' => [
            'addressLine1' => '789 Customer St',
            'city' => 'New York',
            'stateOrProvince' => 'NY',
            'postalCode' => '10001',
            'countryCode' => 'US',
        ],
    ],
]);

// Step 2: Find the cheapest rate
$cheapestRate = null;
$lowestCost = PHP_FLOAT_MAX;

foreach ($quote->rates ?? [] as $rate) {
    $cost = (float) ($rate->shippingCost?->value ?? 0);
    if ($cost < $lowestCost) {
        $lowestCost = $cost;
        $cheapestRate = $rate;
    }
}

// Step 3: Create shipment with selected rate
$shipment = Ebay::logistics()->createFromShippingQuote([
    'shippingQuoteId' => $quote->shippingQuoteId,
    'rateId' => $cheapestRate->rateId,
    'labelSize' => '4"x6"',
]);

// Step 4: Download and save label
$labelPdf = Ebay::logistics()->downloadLabelFile($shipment->shipmentId);
$filename = "label-{$shipment->shipmentId}.pdf";
Storage::put("shipping-labels/{$filename}", $labelPdf);

// Step 5: Update order with tracking number
Ebay::fulfillment()->createShippingFulfillment($quote->orderId, [
    'lineItems' => [/* ... */],
    'shippedDate' => now()->toIso8601String(),
    'shippingCarrierCode' => $shipment->rate?->shippingCarrierCode,
    'trackingNumber' => $shipment->shipmentTrackingNumber,
]);

Log::info('Shipping label created', [
    'shipment_id' => $shipment->shipmentId,
    'tracking_number' => $shipment->shipmentTrackingNumber,
    'cost' => $shipment->rate?->shippingCost?->value,
]);
```

### OAuth Scope

Logistics API requires the following OAuth scope:

- **All operations**: `https://api.ebay.com/oauth/api_scope/sell.logistics`

Add to `config/ebay.php`:

```php
'scopes' => [
    'https://api.ebay.com/oauth/api_scope/sell.logistics',
    // ... other scopes
],
```

## Message API

### Get Conversations

```php
use Tigusigalpa\Ebay\Enums\{ConversationType, ConversationStatus};

$result = Ebay::message()->getConversations([
    'conversation_type' => ConversationType::FROM_MEMBERS->value,
    'conversation_status' => ConversationStatus::UNREAD->value,
    'limit' => 25,
]);

foreach ($result['conversations'] as $conversation) {
    echo $conversation->conversationTitle;
    echo $conversation->latestMessage?->messageBody;
}
```

### Send Message

```php
// Start new conversation
$message = Ebay::message()->sendMessage([
    'otherPartyUsername' => 'buyer_username',
    'messageText' => 'Thank you for your question.',
    'reference' => [
        'referenceId' => '123456789',
        'referenceType' => 'LISTING',
    ],
]);

// Reply to conversation
$message = Ebay::message()->sendMessage([
    'conversationId' => 'c1234567890',
    'messageText' => 'Here is the information you requested.',
]);
```

### Manage Conversations

```php
// Mark as read
Ebay::message()->updateConversation([
    'conversationId' => 'c1234567890',
    'conversationType' => ConversationType::FROM_MEMBERS->value,
    'read' => true,
]);

// Archive multiple conversations
$result = Ebay::message()->bulkUpdateConversation([
    'conversations' => [
        [
            'conversationId' => 'c1111111111',
            'conversationType' => ConversationType::FROM_MEMBERS->value,
            'conversationStatus' => 'ARCHIVE',
        ],
    ],
]);
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
use Tigusigalpa\Ebay\Enums\{
    Site, 
    Currency, 
    ListingStatus, 
    OrderStatus, 
    PaymentStatus, 
    ListingType,
    ReasonForRefundEnum,
    DisputeStatusEnum,
    EvidenceTypeEnum,
    SellerDecisionEnum
};

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

// Fulfillment API Enums
$reason = ReasonForRefundEnum::BUYER_CANCEL;
$reason->title();         // "Buyer Cancel"

$disputeStatus = DisputeStatusEnum::OPEN;
$disputeStatus->title();  // "Open"

$evidenceType = EvidenceTypeEnum::PROOF_OF_DELIVERY;
$evidenceType->title();   // "Proof of Delivery"
```

### DTOs

```php
use Tigusigalpa\Ebay\Http\Resources\{Order, Item};
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\{
    Order as FulfillmentOrder,
    ShippingFulfillment,
    PaymentDispute
};

// Trading API DTOs
$xml = Ebay::trading()->getOrders();
foreach ($xml->OrderArray->Order as $orderXml) {
    $order = Order::fromXml($orderXml);
    
    echo $order->orderId;
    echo $order->total;
    echo $order->orderStatus->title();
}

// Fulfillment API DTOs (immutable with readonly properties)
$order = Ebay::fulfillment()->getOrder('12-34567-89012');

// Access nested DTOs
echo $order->buyer?->username;
echo $order->pricingSummary?->total?->value;
echo $order->pricingSummary?->total?->currency;

// Line items
foreach ($order->lineItems ?? [] as $lineItem) {
    echo $lineItem->title;
    echo $lineItem->lineItemCost?->value;
    echo $lineItem->quantity;
}

// Payment disputes
$dispute = Ebay::fulfillment()->getPaymentDispute('5001234567890');
echo $dispute->paymentDisputeStatus?->title();
echo $dispute->amount?->value;

// All DTOs use static fromArray() factory method
$orderData = ['orderId' => '12-34567-89012', /* ... */];
$order = FulfillmentOrder::fromArray($orderData);
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

- **Wiki**: [Documentation & Guides](https://github.com/tigusigalpa/ebay-php/wiki)
- **GitHub Issues**: [tigusigalpa/ebay-php/issues](https://github.com/tigusigalpa/ebay-php/issues)
- **Discussions**: [GitHub Discussions](https://github.com/tigusigalpa/ebay-php/discussions)
- **Email**: sovletig@gmail.com

## eBay Documentation

- [Trading API Reference](https://developer.ebay.com/devzone/xml/docs/Reference/ebay/index.html)
- [Commerce API Reference](https://developer.ebay.com/api-docs/commerce/static/overview.html)
- [Fulfillment API Reference](https://developer.ebay.com/api-docs/sell/fulfillment/resources/methods)
- [Logistics API Reference](https://developer.ebay.com/api-docs/sell/logistics/resources/methods)
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
