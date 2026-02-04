<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Clients;

use Illuminate\Support\Facades\Http;
use Tigusigalpa\Ebay\Exceptions\EbayApiException;

/**
 * eBay Commerce API Client (REST-based)
 * 
 * @link https://developer.ebay.com/api-docs/static/rest-request-components.html
 */
class CommerceClient extends BaseClient
{
    protected const BASE_URLS = [
        'sandbox' => 'https://api.sandbox.ebay.com',
        'production' => 'https://api.ebay.com',
    ];

    /**
     * Make a REST API call
     */
    protected function request(
        string $method,
        string $endpoint,
        array $data = [],
        array $headers = [],
        bool $requiresAuth = true
    ): array {
        $url = self::BASE_URLS[$this->environment] . $endpoint;

        $this->log("Commerce API Request: {$method} {$endpoint}", [
            'method' => $method,
            'endpoint' => $endpoint,
        ]);

        $client = Http::withHeaders(array_merge([
            'Accept' => 'application/json',
            'Accept-Charset' => 'utf-8',
            'Accept-Language' => $this->site->locale(),
            'X-EBAY-C-MARKETPLACE-ID' => $this->site->marketplace(),
        ], $headers));

        if ($requiresAuth) {
            $token = $this->getAccessToken();
            if (!$token) {
                throw new EbayApiException(
                    'Access token required but not available',
                    'missing_token'
                );
            }
            $client = $client->withToken($token);
        }

        $response = match(strtolower($method)) {
            'get' => $client->get($url, $data),
            'post' => $client->post($url, $data),
            'put' => $client->put($url, $data),
            'patch' => $client->patch($url, $data),
            'delete' => $client->delete($url, $data),
            default => throw new EbayApiException("Unsupported HTTP method: {$method}", 'invalid_method'),
        };

        if ($response->failed()) {
            $error = $response->json();
            $errors = [];
            
            if (isset($error['errors']) && is_array($error['errors'])) {
                foreach ($error['errors'] as $err) {
                    $errors[] = [
                        'code' => $err['errorId'] ?? $err['error'] ?? 'unknown',
                        'message' => $err['message'] ?? $err['error_description'] ?? 'Unknown error',
                        'domain' => $err['domain'] ?? '',
                        'category' => $err['category'] ?? '',
                    ];
                }
            } else {
                $errors[] = [
                    'code' => $error['error'] ?? 'unknown',
                    'message' => $error['error_description'] ?? $error['message'] ?? 'Unknown error',
                ];
            }

            $this->handleError(
                $errors[0]['message'] ?? 'Commerce API request failed',
                $errors,
                $response->body()
            );
        }

        return $response->json() ?? [];
    }

    /**
     * Get item aspects for a category
     * 
     * @link https://developer.ebay.com/api-docs/commerce/taxonomy/resources/category_tree/methods/getItemAspectsForCategory
     */
    public function getItemAspectsForCategory(string $categoryTreeId, string $categoryId): array
    {
        return $this->request(
            'GET',
            "/commerce/taxonomy/v1/category_tree/{$categoryTreeId}/get_item_aspects_for_category",
            ['category_id' => $categoryId],
            [],
            false
        );
    }

    /**
     * Translate text
     * 
     * @link https://developer.ebay.com/api-docs/commerce/translation/overview.html
     * @link https://developer.ebay.com/api-docs/commerce/translation/resources/language/methods/translate
     */
    public function translate(
        string $text,
        string $fromLanguage,
        string $toLanguage,
        string $context = 'ITEM_TITLE'
    ): ?string {
        $response = $this->request(
            'POST',
            '/commerce/translation/v1_beta/translate',
            [
                'from' => $fromLanguage,
                'to' => $toLanguage,
                'text' => [$text],
                'translationContext' => $context,
            ]
        );

        if (isset($response['translations'][0]['translatedText'])) {
            return $response['translations'][0]['translatedText'];
        }

        return null;
    }

    /**
     * Get inventory item
     * 
     * @link https://developer.ebay.com/api-docs/sell/inventory/resources/inventory_item/methods/getInventoryItem
     */
    public function getInventoryItem(string $sku): array
    {
        return $this->request(
            'GET',
            "/sell/inventory/v1/inventory_item/{$sku}"
        );
    }

    /**
     * Create or replace inventory item
     * 
     * @link https://developer.ebay.com/api-docs/sell/inventory/resources/inventory_item/methods/createOrReplaceInventoryItem
     */
    public function createOrReplaceInventoryItem(string $sku, array $itemData): array
    {
        return $this->request(
            'PUT',
            "/sell/inventory/v1/inventory_item/{$sku}",
            $itemData
        );
    }

    /**
     * Get fulfillment order
     * 
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/methods/getOrder
     */
    public function getFulfillmentOrder(string $orderId): array
    {
        return $this->request(
            'GET',
            "/sell/fulfillment/v1/order/{$orderId}"
        );
    }

    /**
     * Get fulfillment orders
     * 
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/methods/getOrders
     */
    public function getFulfillmentOrders(array $params = []): array
    {
        return $this->request(
            'GET',
            '/sell/fulfillment/v1/order',
            $params
        );
    }
}
