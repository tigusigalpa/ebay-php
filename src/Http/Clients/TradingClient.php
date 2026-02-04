<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Clients;

use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use Tigusigalpa\Ebay\Exceptions\EbayApiException;

/**
 * eBay Trading API Client (XML-based)
 * 
 * @link https://developer.ebay.com/devzone/xml/docs/Concepts/MakingACall.html
 * @link https://developer.ebay.com/devzone/xml/docs/Reference/ebay/index.html
 */
class TradingClient extends BaseClient
{
    protected const API_ENDPOINTS = [
        'sandbox' => 'https://api.sandbox.ebay.com/ws/api.dll',
        'production' => 'https://api.ebay.com/ws/api.dll',
    ];

    protected int $compatibilityLevel;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->compatibilityLevel = config('ebay.compatibility_level', 1257);
    }

    /**
     * Call a Trading API method
     * 
     * @link https://developer.ebay.com/devzone/xml/docs/Concepts/MakingACall.html#CallParameters
     */
    public function call(
        string $method,
        array $data = [],
        bool $requiresAuth = false,
        ?int $version = null
    ): SimpleXMLElement {
        $requestBody = $this->buildRequestBody($method, $data, $requiresAuth, $version);
        
        $this->log("Trading API Request: {$method}", [
            'method' => $method,
            'site' => $this->site->value,
        ]);

        $headers = $this->buildHeaders($method);
        
        $response = Http::withHeaders($headers)
            ->withBody($requestBody, 'text/xml')
            ->post(self::API_ENDPOINTS[$this->environment]);

        if ($response->failed()) {
            throw new EbayApiException(
                "Trading API request failed: {$method}",
                'request_failed',
                $response->body()
            );
        }

        $xml = simplexml_load_string($response->body());
        
        if (!$xml) {
            throw new EbayApiException(
                'Failed to parse XML response',
                'invalid_xml',
                $response->body()
            );
        }

        $this->checkForErrors($xml, $response->body());

        return $xml;
    }

    /**
     * Get orders
     * 
     * @link https://developer.ebay.com/Devzone/XML/docs/Reference/eBay/GetOrders.html
     */
    public function getOrders(array $params = []): SimpleXMLElement
    {
        return $this->call('GetOrders', $params, true);
    }

    /**
     * Get categories
     * 
     * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/GetCategories.html
     */
    public function getCategories(array $params = []): SimpleXMLElement
    {
        return $this->call('GetCategories', $params, false);
    }

    /**
     * Get category features
     * 
     * @link https://developer.ebay.com/DevZone/XML/docs/Reference/eBay/GetCategoryFeatures.html
     */
    public function getCategoryFeatures(string $categoryId): SimpleXMLElement
    {
        return $this->call('GetCategoryFeatures', [
            'CategoryID' => $categoryId,
            'DetailLevel' => 'ReturnAll',
        ], false);
    }

    /**
     * Get item details
     * 
     * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/getitem.html
     */
    public function getItem(string $itemId): SimpleXMLElement
    {
        return $this->call('GetItem', [
            'ItemID' => $itemId,
        ], false);
    }

    /**
     * Add fixed price item
     * 
     * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/AddFixedPriceItem.html
     */
    public function addFixedPriceItem(array $itemData): SimpleXMLElement
    {
        return $this->call('AddFixedPriceItem', ['Item' => $itemData], true);
    }

    /**
     * Get My eBay Selling
     * 
     * @link https://developer.ebay.com/devzone/xml/docs/reference/ebay/getmyebayselling.html
     */
    public function getMyEbaySelling(array $params = []): SimpleXMLElement
    {
        return $this->call('GetMyEbaySelling', $params, true);
    }

    /**
     * Build XML request body
     */
    protected function buildRequestBody(
        string $method,
        array $data,
        bool $requiresAuth,
        ?int $version
    ): string {
        $requestData = [
            'Version' => $version ?? $this->compatibilityLevel,
            'WarningLevel' => 'High',
            'ErrorLanguage' => $this->site->language(),
        ];

        if ($requiresAuth) {
            $token = $this->getAccessToken();
            if (!$token) {
                throw new EbayApiException(
                    'Access token required but not available',
                    'missing_token'
                );
            }
            $requestData['RequesterCredentials'] = [
                'eBayAuthToken' => $token,
            ];
        }

        $requestData = array_merge($requestData, $data);

        return ArrayToXml::convert(
            $requestData,
            [
                'rootElementName' => $method . 'Request',
                '_attributes' => [
                    'xmlns' => 'urn:ebay:apis:eBLBaseComponents',
                ],
            ],
            true,
            'utf-8'
        );
    }

    /**
     * Build request headers
     */
    protected function buildHeaders(string $method): array
    {
        $headers = [
            'X-EBAY-API-SITEID' => (string) $this->site->value,
            'X-EBAY-API-COMPATIBILITY-LEVEL' => (string) $this->compatibilityLevel,
            'X-EBAY-API-CALL-NAME' => $method,
            'X-EBAY-API-APP-NAME' => $this->appId,
            'X-EBAY-API-DEV-NAME' => $this->devId,
            'X-EBAY-API-CERT-NAME' => $this->certId,
        ];

        if ($token = $this->getAccessToken()) {
            $headers['X-EBAY-API-IAF-TOKEN'] = $token;
        }

        return $headers;
    }

    /**
     * Check XML response for errors
     */
    protected function checkForErrors(SimpleXMLElement $xml, string $rawResponse): void
    {
        if (!isset($xml->Ack)) {
            return;
        }

        $ack = (string) $xml->Ack;

        if ($ack === 'Failure' || $ack === 'PartialFailure') {
            $errors = [];
            
            if (isset($xml->Errors)) {
                foreach ($xml->Errors as $error) {
                    $errors[] = [
                        'code' => (string) $error->ErrorCode,
                        'severity' => (string) $error->SeverityCode,
                        'short_message' => (string) $error->ShortMessage,
                        'long_message' => (string) $error->LongMessage,
                        'classification' => (string) ($error->ErrorClassification ?? ''),
                    ];
                }
            }

            $this->handleError(
                $errors[0]['long_message'] ?? 'eBay API error',
                $errors,
                $rawResponse
            );
        }
    }

    public function setCompatibilityLevel(int $level): self
    {
        $this->compatibilityLevel = $level;
        return $this;
    }
}
