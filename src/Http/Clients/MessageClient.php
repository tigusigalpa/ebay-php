<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Clients;

use Illuminate\Support\Facades\Http;
use Tigusigalpa\Ebay\Exceptions\EbayApiException;
use Tigusigalpa\Ebay\Http\Resources\Conversation;
use Tigusigalpa\Ebay\Http\Resources\Message;

/**
 * eBay Commerce Message API Client (REST-based)
 * 
 * @link https://developer.ebay.com/api-docs/commerce/message/overview.html
 */
class MessageClient extends BaseClient
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

        $this->log("Message API Request: {$method} {$endpoint}", [
            'method' => $method,
            'endpoint' => $endpoint,
        ]);

        $client = Http::withHeaders(array_merge([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
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
                $errors[0]['message'] ?? 'Message API request failed',
                $errors,
                $response->body()
            );
        }

        return $response->json() ?? [];
    }

    /**
     * Retrieve conversations with optional filters
     * 
     * @param array $params Query parameters including conversation_type (required), conversation_status, limit, offset, etc.
     * @return array Contains 'conversations' array of Conversation objects and pagination metadata
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/commerce/message/resources/conversation/methods/getConversations
     */
    public function getConversations(array $params = []): array
    {
        $response = $this->request(
            'GET',
            '/commerce/message/v1/conversation',
            $params
        );

        $conversations = [];
        if (isset($response['conversations']) && is_array($response['conversations'])) {
            foreach ($response['conversations'] as $conversationData) {
                $conversations[] = Conversation::fromArray($conversationData);
            }
        }

        return [
            'conversations' => $conversations,
            'href' => $response['href'] ?? null,
            'limit' => $response['limit'] ?? null,
            'next' => $response['next'] ?? null,
            'offset' => $response['offset'] ?? null,
            'prev' => $response['prev'] ?? null,
            'total' => $response['total'] ?? null,
        ];
    }

    /**
     * Get messages in a specific conversation
     * 
     * @param string $conversationId Unique conversation ID
     * @param array $params Query parameters including conversation_type (required), limit, offset
     * @return array Contains 'messages' array of Message objects and pagination metadata
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/commerce/message/resources/conversation/methods/getConversation
     */
    public function getConversation(string $conversationId, array $params = []): array
    {
        $response = $this->request(
            'GET',
            "/commerce/message/v1/conversation/{$conversationId}",
            $params
        );

        $messages = [];
        if (isset($response['messages']) && is_array($response['messages'])) {
            foreach ($response['messages'] as $messageData) {
                $messages[] = Message::fromArray($messageData);
            }
        }

        return [
            'messages' => $messages,
            'href' => $response['href'] ?? null,
            'limit' => $response['limit'] ?? null,
            'next' => $response['next'] ?? null,
            'offset' => $response['offset'] ?? null,
            'prev' => $response['prev'] ?? null,
            'total' => $response['total'] ?? null,
        ];
    }

    /**
     * Send a new message or reply in a conversation
     * 
     * @param array $data Request body with messageText (required), conversationId or otherPartyUsername, etc.
     * @return Message The sent message object
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/commerce/message/resources/conversation/methods/sendMessage
     */
    public function sendMessage(array $data): Message
    {
        $response = $this->request(
            'POST',
            '/commerce/message/v1/send_message',
            $data
        );

        return Message::fromArray($response);
    }

    /**
     * Update the status of a single conversation
     * 
     * @param array $data Request body with conversationId, conversationType, conversationStatus or read flag
     * @return void Returns 204 No Content on success
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/commerce/message/resources/conversation/methods/updateConversation
     */
    public function updateConversation(array $data): void
    {
        $this->request(
            'POST',
            '/commerce/message/v1/update_conversation',
            $data
        );
    }

    /**
     * Update statuses of up to 10 conversations in bulk
     * 
     * @param array $data Request body with conversations array (up to 10 items)
     * @return array Response with conversationsMetadata including successCount and failureCount
     * @throws EbayApiException
     * @link https://developer.ebay.com/api-docs/commerce/message/resources/conversation/methods/bulkUpdateConversation
     */
    public function bulkUpdateConversation(array $data): array
    {
        return $this->request(
            'POST',
            '/commerce/message/v1/bulk_update_conversation',
            $data
        );
    }
}
