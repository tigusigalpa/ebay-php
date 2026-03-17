<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Clients;

use Illuminate\Support\Facades\Http;
use Tigusigalpa\Ebay\Exceptions\EbayApiException;
use Tigusigalpa\Ebay\Http\Resources\Logistics\Shipment;
use Tigusigalpa\Ebay\Http\Resources\Logistics\ShippingQuote;

/**
 * eBay Logistics API Client (REST-based)
 * 
 * Provides access to the eBay Sell Logistics API v1_beta
 * 
 * @link https://developer.ebay.com/api-docs/sell/logistics/resources/methods
 */
class LogisticsClient extends BaseClient
{
    protected const BASE_URLS = [
        'sandbox' => 'https://api.sandbox.ebay.com/sell/logistics/v1_beta',
        'production' => 'https://api.ebay.com/sell/logistics/v1_beta',
    ];

    /**
     * Make a REST API call
     * 
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param array $headers Additional headers
     * @param bool $requiresAuth Whether authentication is required
     * @return array Response data
     * @throws EbayApiException
     */
    protected function request(
        string $method,
        string $endpoint,
        array $data = [],
        array $headers = [],
        bool $requiresAuth = true
    ): array {
        $baseUrl = self::BASE_URLS[$this->environment];
        $url = $baseUrl . $endpoint;

        $this->log("Logistics API Request: {$method} {$endpoint}", [
            'method' => $method,
            'endpoint' => $endpoint,
        ]);

        $client = Http::withHeaders(array_merge([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-Charset' => 'utf-8',
            'Accept-Language' => $this->site->locale(),
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
                $errors[0]['message'] ?? 'Logistics API request failed',
                $errors,
                $response->body()
            );
        }

        return $response->json() ?? [];
    }

    /**
     * Cancel a shipment
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.logistics
     * 
     * @param string $shipmentId The unique identifier of the shipment to cancel
     * @return void
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipment/methods/cancelShipment
     */
    public function cancelShipment(string $shipmentId): void
    {
        $baseUrl = self::BASE_URLS[$this->environment];
        $url = $baseUrl . "/shipment/{$shipmentId}/cancel";

        $this->log("Logistics API Request: POST /shipment/{$shipmentId}/cancel");

        $token = $this->getAccessToken();
        if (!$token) {
            throw new EbayApiException(
                'Access token required but not available',
                'missing_token'
            );
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-Charset' => 'utf-8',
            'Accept-Language' => $this->site->locale(),
        ])
        ->withToken($token)
        ->post($url);

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
                $errors[0]['message'] ?? 'Cancel shipment failed',
                $errors,
                $response->body()
            );
        }
    }

    /**
     * Create a shipment from a shipping quote
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.logistics
     * 
     * @param array $data CreateShipmentFromQuoteRequest payload
     * @return Shipment
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipment/methods/createFromShippingQuote
     */
    public function createFromShippingQuote(array $data): Shipment
    {
        $response = $this->request(
            'POST',
            '/shipment/create_from_shipping_quote',
            $data
        );

        return Shipment::fromArray($response);
    }

    /**
     * Download a shipping label file
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.logistics
     * 
     * @param string $shipmentId The unique identifier of the shipment
     * @param string $accept Accept header (application/pdf or application/zpl)
     * @return string Raw binary file content
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipment/methods/downloadLabelFile
     */
    public function downloadLabelFile(string $shipmentId, string $accept = 'application/pdf'): string
    {
        $baseUrl = self::BASE_URLS[$this->environment];
        $url = $baseUrl . "/shipment/{$shipmentId}/download_label_file";

        $this->log("Logistics API Request: GET /shipment/{$shipmentId}/download_label_file");

        $token = $this->getAccessToken();
        if (!$token) {
            throw new EbayApiException(
                'Access token required but not available',
                'missing_token'
            );
        }

        $response = Http::withHeaders([
            'Accept' => $accept,
            'Accept-Charset' => 'utf-8',
            'Accept-Language' => $this->site->locale(),
        ])
        ->withToken($token)
        ->get($url);

        if ($response->failed()) {
            throw new EbayApiException(
                'Failed to download label file',
                'download_label_failed',
                $response->body()
            );
        }

        return $response->body();
    }

    /**
     * Get a shipment
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.logistics
     * 
     * @param string $shipmentId The unique identifier of the shipment
     * @return Shipment
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipment/methods/getShipment
     */
    public function getShipment(string $shipmentId): Shipment
    {
        $response = $this->request(
            'GET',
            "/shipment/{$shipmentId}"
        );

        return Shipment::fromArray($response);
    }

    /**
     * Create a shipping quote
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.logistics
     * 
     * @param array $data ShippingQuoteRequest payload
     * @return ShippingQuote
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipping_quote/methods/createShippingQuote
     */
    public function createShippingQuote(array $data): ShippingQuote
    {
        $response = $this->request(
            'POST',
            '/shipping_quote',
            $data
        );

        return ShippingQuote::fromArray($response);
    }

    /**
     * Get a shipping quote
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.logistics
     * 
     * @param string $shippingQuoteId The unique identifier of the shipping quote
     * @return ShippingQuote
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/logistics/resources/shipping_quote/methods/getShippingQuote
     */
    public function getShippingQuote(string $shippingQuoteId): ShippingQuote
    {
        $response = $this->request(
            'GET',
            "/shipping_quote/{$shippingQuoteId}"
        );

        return ShippingQuote::fromArray($response);
    }
}
