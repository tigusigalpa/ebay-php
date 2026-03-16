<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Clients;

use Illuminate\Support\Facades\Http;
use Tigusigalpa\Ebay\Exceptions\EbayApiException;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\Order;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\OrderSearchPagedCollection;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\Refund;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\ShippingFulfillment;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\ShippingFulfillmentPagedCollection;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\PaymentDispute;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\DisputeSummaryResponse;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\PaymentDisputeActivityHistory;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\AddEvidencePaymentDisputeResponse;
use Tigusigalpa\Ebay\Http\Resources\Fulfillment\UploadEvidenceFileResponse;

/**
 * eBay Fulfillment API Client (REST-based)
 * 
 * Provides access to the eBay Fulfillment API v1.20.6
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/methods
 */
class FulfillmentClient extends BaseClient
{
    protected const BASE_URLS = [
        'sandbox' => 'https://api.sandbox.ebay.com/sell/fulfillment/v1',
        'production' => 'https://api.ebay.com/sell/fulfillment/v1',
    ];

    protected const DISPUTE_BASE_URLS = [
        'sandbox' => 'https://apiz.sandbox.ebay.com/sell/fulfillment/v1',
        'production' => 'https://apiz.ebay.com/sell/fulfillment/v1',
    ];

    /**
     * Make a REST API call
     * 
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param array $headers Additional headers
     * @param bool $requiresAuth Whether authentication is required
     * @param bool $useDisputeHost Whether to use dispute-specific host
     * @return array Response data
     * @throws EbayApiException
     */
    protected function request(
        string $method,
        string $endpoint,
        array $data = [],
        array $headers = [],
        bool $requiresAuth = true,
        bool $useDisputeHost = false
    ): array {
        $baseUrl = $useDisputeHost 
            ? self::DISPUTE_BASE_URLS[$this->environment] 
            : self::BASE_URLS[$this->environment];
        
        $url = $baseUrl . $endpoint;

        $this->log("Fulfillment API Request: {$method} {$endpoint}", [
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
                $errors[0]['message'] ?? 'Fulfillment API request failed',
                $errors,
                $response->body()
            );
        }

        return $response->json() ?? [];
    }

    /**
     * Get a single order by ID
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly
     * 
     * @param string $orderId The unique identifier of the order
     * @param array $query Optional query parameters (e.g., ['fieldGroups' => 'TAX_BREAKDOWN'])
     * @return Order
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/methods/getOrder
     */
    public function getOrder(string $orderId, array $query = []): Order
    {
        $response = $this->request(
            'GET',
            "/order/{$orderId}",
            $query
        );

        return Order::fromArray($response);
    }

    /**
     * Get multiple orders
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly
     * 
     * @param array $query Query parameters (orderIds, filter, limit, offset, fieldGroups)
     * @return OrderSearchPagedCollection
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/methods/getOrders
     */
    public function getOrders(array $query = []): OrderSearchPagedCollection
    {
        $response = $this->request(
            'GET',
            '/order',
            $query
        );

        return OrderSearchPagedCollection::fromArray($response);
    }

    /**
     * Issue a refund for an order
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.finances
     * 
     * @param string $orderId The unique identifier of the order
     * @param array $payload Refund request payload
     * @return Refund
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/methods/issueRefund
     */
    public function issueRefund(string $orderId, array $payload): Refund
    {
        $response = $this->request(
            'POST',
            "/order/{$orderId}/issue_refund",
            $payload
        );

        return Refund::fromArray($response);
    }

    /**
     * Create a shipping fulfillment
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.fulfillment
     * 
     * @param string $orderId The unique identifier of the order
     * @param array $payload Shipping fulfillment details
     * @return string The fulfillment ID extracted from the Location header
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/shipping_fulfillment/methods/createShippingFulfillment
     */
    public function createShippingFulfillment(string $orderId, array $payload): string
    {
        $baseUrl = self::BASE_URLS[$this->environment];
        $url = $baseUrl . "/order/{$orderId}/shipping_fulfillment";

        $this->log("Fulfillment API Request: POST /order/{$orderId}/shipping_fulfillment", [
            'method' => 'POST',
            'endpoint' => "/order/{$orderId}/shipping_fulfillment",
        ]);

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
        ->post($url, $payload);

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
                $errors[0]['message'] ?? 'Create shipping fulfillment failed',
                $errors,
                $response->body()
            );
        }

        $location = $response->header('Location');
        if (!$location) {
            throw new EbayApiException(
                'Location header not found in response',
                'missing_location_header'
            );
        }

        $parts = explode('/', $location);
        return end($parts);
    }

    /**
     * Get a shipping fulfillment
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly
     * 
     * @param string $orderId The unique identifier of the order
     * @param string $fulfillmentId The unique identifier of the fulfillment
     * @return ShippingFulfillment
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/shipping_fulfillment/methods/getShippingFulfillment
     */
    public function getShippingFulfillment(string $orderId, string $fulfillmentId): ShippingFulfillment
    {
        $response = $this->request(
            'GET',
            "/order/{$orderId}/shipping_fulfillment/{$fulfillmentId}"
        );

        return ShippingFulfillment::fromArray($response);
    }

    /**
     * Get all shipping fulfillments for an order
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly
     * 
     * @param string $orderId The unique identifier of the order
     * @return ShippingFulfillmentPagedCollection
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/shipping_fulfillment/methods/getShippingFulfillments
     */
    public function getShippingFulfillments(string $orderId): ShippingFulfillmentPagedCollection
    {
        $response = $this->request(
            'GET',
            "/order/{$orderId}/shipping_fulfillment"
        );

        return ShippingFulfillmentPagedCollection::fromArray($response);
    }

    /**
     * Get a payment dispute
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param string $paymentDisputeId The unique identifier of the payment dispute
     * @return PaymentDispute
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/getPaymentDispute
     */
    public function getPaymentDispute(string $paymentDisputeId): PaymentDispute
    {
        $response = $this->request(
            'GET',
            "/payment_dispute/{$paymentDisputeId}",
            [],
            [],
            true,
            true
        );

        return PaymentDispute::fromArray($response);
    }

    /**
     * Fetch evidence file content
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param string $paymentDisputeId The unique identifier of the payment dispute
     * @param string $evidenceId The unique identifier of the evidence
     * @param string $fileId The unique identifier of the file
     * @return string Raw file content
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/fetchEvidenceContent
     */
    public function fetchEvidenceContent(string $paymentDisputeId, string $evidenceId, string $fileId): string
    {
        $baseUrl = self::DISPUTE_BASE_URLS[$this->environment];
        $url = $baseUrl . "/payment_dispute/{$paymentDisputeId}/evidence/{$evidenceId}/file/{$fileId}";

        $this->log("Fulfillment API Request: GET /payment_dispute/{$paymentDisputeId}/evidence/{$evidenceId}/file/{$fileId}");

        $token = $this->getAccessToken();
        if (!$token) {
            throw new EbayApiException(
                'Access token required but not available',
                'missing_token'
            );
        }

        $response = Http::withToken($token)->get($url);

        if ($response->failed()) {
            throw new EbayApiException(
                'Failed to fetch evidence content',
                'fetch_evidence_failed',
                $response->body()
            );
        }

        return $response->body();
    }

    /**
     * Get payment dispute activities
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param string $paymentDisputeId The unique identifier of the payment dispute
     * @return PaymentDisputeActivityHistory
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/getActivities
     */
    public function getActivities(string $paymentDisputeId): PaymentDisputeActivityHistory
    {
        $response = $this->request(
            'GET',
            "/payment_dispute/{$paymentDisputeId}/activity",
            [],
            [],
            true,
            true
        );

        return PaymentDisputeActivityHistory::fromArray($response);
    }

    /**
     * Get payment dispute summaries
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param array $query Query parameters (order_id, buyer_username, open_date_from, open_date_to, payment_dispute_status, limit, offset)
     * @return DisputeSummaryResponse
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/getPaymentDisputeSummaries
     */
    public function getPaymentDisputeSummaries(array $query = []): DisputeSummaryResponse
    {
        $response = $this->request(
            'GET',
            '/payment_dispute_summary',
            $query,
            [],
            true,
            true
        );

        return DisputeSummaryResponse::fromArray($response);
    }

    /**
     * Contest a payment dispute
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param string $paymentDisputeId The unique identifier of the payment dispute
     * @param array $payload Contest request payload (must include revision field)
     * @return void
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/contestPaymentDispute
     */
    public function contestPaymentDispute(string $paymentDisputeId, array $payload): void
    {
        $this->request(
            'POST',
            "/payment_dispute/{$paymentDisputeId}/contest",
            $payload,
            [],
            true,
            true
        );
    }

    /**
     * Accept a payment dispute
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param string $paymentDisputeId The unique identifier of the payment dispute
     * @param array $payload Optional accept request payload
     * @return void
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/acceptPaymentDispute
     */
    public function acceptPaymentDispute(string $paymentDisputeId, array $payload = []): void
    {
        $this->request(
            'POST',
            "/payment_dispute/{$paymentDisputeId}/accept",
            $payload,
            [],
            true,
            true
        );
    }

    /**
     * Upload an evidence file
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param string $paymentDisputeId The unique identifier of the payment dispute
     * @param mixed $fileData File data to upload
     * @return UploadEvidenceFileResponse
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/uploadEvidenceFile
     */
    public function uploadEvidenceFile(string $paymentDisputeId, mixed $fileData): UploadEvidenceFileResponse
    {
        $baseUrl = self::DISPUTE_BASE_URLS[$this->environment];
        $url = $baseUrl . "/payment_dispute/{$paymentDisputeId}/upload_evidence_file";

        $this->log("Fulfillment API Request: POST /payment_dispute/{$paymentDisputeId}/upload_evidence_file");

        $token = $this->getAccessToken();
        if (!$token) {
            throw new EbayApiException(
                'Access token required but not available',
                'missing_token'
            );
        }

        $response = Http::withToken($token)
            ->attach('file', $fileData)
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
                $errors[0]['message'] ?? 'Upload evidence file failed',
                $errors,
                $response->body()
            );
        }

        return UploadEvidenceFileResponse::fromArray($response->json() ?? []);
    }

    /**
     * Add evidence to a payment dispute
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param string $paymentDisputeId The unique identifier of the payment dispute
     * @param array $payload Evidence payload (evidenceType, files, lineItems)
     * @return AddEvidencePaymentDisputeResponse
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/addEvidence
     */
    public function addEvidence(string $paymentDisputeId, array $payload): AddEvidencePaymentDisputeResponse
    {
        $response = $this->request(
            'POST',
            "/payment_dispute/{$paymentDisputeId}/add_evidence",
            $payload,
            [],
            true,
            true
        );

        return AddEvidencePaymentDisputeResponse::fromArray($response);
    }

    /**
     * Update evidence for a payment dispute
     * 
     * OAuth scope: https://api.ebay.com/oauth/api_scope/sell.payment.dispute
     * 
     * @param string $paymentDisputeId The unique identifier of the payment dispute
     * @param array $payload Update evidence payload (evidenceId, evidenceType, files, lineItems)
     * @return void
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/updateEvidence
     */
    public function updateEvidence(string $paymentDisputeId, array $payload): void
    {
        $this->request(
            'POST',
            "/payment_dispute/{$paymentDisputeId}/update_evidence",
            $payload,
            [],
            true,
            true
        );
    }
}
