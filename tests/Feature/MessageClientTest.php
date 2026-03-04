<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tigusigalpa\Ebay\Enums\ConversationStatus;
use Tigusigalpa\Ebay\Enums\ConversationType;
use Tigusigalpa\Ebay\Enums\MessageMediaType;
use Tigusigalpa\Ebay\Exceptions\EbayApiException;
use Tigusigalpa\Ebay\Facades\Ebay;
use Tigusigalpa\Ebay\Http\Resources\Conversation;
use Tigusigalpa\Ebay\Http\Resources\Message;
use Tigusigalpa\Ebay\Tests\TestCase;

class MessageClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        Ebay::setAccessToken('test_access_token', time() + 7200);
    }

    public function test_get_conversations_returns_conversation_objects()
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Message/getConversations.json'),
            true
        );

        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/conversation*' => Http::response($fixture, 200),
        ]);

        $result = Ebay::message()->getConversations([
            'conversation_type' => ConversationType::FROM_MEMBERS->value,
            'conversation_status' => ConversationStatus::ACTIVE->value,
            'limit' => 25,
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('conversations', $result);
        $this->assertCount(2, $result['conversations']);
        
        $this->assertInstanceOf(Conversation::class, $result['conversations'][0]);
        $this->assertEquals('c1234567890', $result['conversations'][0]->conversationId);
        $this->assertEquals(ConversationStatus::ACTIVE, $result['conversations'][0]->conversationStatus);
        $this->assertEquals(ConversationType::FROM_MEMBERS, $result['conversations'][0]->conversationType);
        $this->assertEquals('Question about item #123456789', $result['conversations'][0]->conversationTitle);
        $this->assertEquals(1, $result['conversations'][0]->unreadCount);
        
        $this->assertInstanceOf(Message::class, $result['conversations'][0]->latestMessage);
        $this->assertEquals('m9876543210', $result['conversations'][0]->latestMessage->messageId);
        $this->assertEquals('Is this item still available?', $result['conversations'][0]->latestMessage->messageBody);
        
        $this->assertEquals(25, $result['limit']);
        $this->assertEquals(0, $result['offset']);
        $this->assertEquals(2, $result['total']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.sandbox.ebay.com/commerce/message/v1/conversation?conversation_type=FROM_MEMBERS&conversation_status=ACTIVE&limit=25'
                && $request->hasHeader('Authorization', 'Bearer test_access_token')
                && $request->hasHeader('Accept', 'application/json')
                && $request->hasHeader('Content-Type', 'application/json');
        });
    }

    public function test_get_conversation_returns_message_objects()
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Message/getConversation.json'),
            true
        );

        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/conversation/c1234567890*' => Http::response($fixture, 200),
        ]);

        $result = Ebay::message()->getConversation(
            'c1234567890',
            ['conversation_type' => ConversationType::FROM_MEMBERS->value]
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('messages', $result);
        $this->assertCount(3, $result['messages']);
        
        $this->assertInstanceOf(Message::class, $result['messages'][0]);
        $this->assertEquals('m9876543210', $result['messages'][0]->messageId);
        $this->assertEquals('Is this item still available?', $result['messages'][0]->messageBody);
        $this->assertEquals('buyer123', $result['messages'][0]->senderUsername);
        $this->assertEquals('seller456', $result['messages'][0]->recipientUsername);
        $this->assertTrue($result['messages'][0]->readStatus);
        
        $this->assertInstanceOf(Message::class, $result['messages'][1]);
        $this->assertCount(1, $result['messages'][1]->messageMedia);
        $this->assertEquals('product_detail.jpg', $result['messages'][1]->messageMedia[0]->mediaName);
        $this->assertEquals(MessageMediaType::IMAGE, $result['messages'][1]->messageMedia[0]->mediaType);
        $this->assertEquals('https://example.ebay.com/media/product_detail.jpg', $result['messages'][1]->messageMedia[0]->mediaUrl);
        
        $this->assertEquals(25, $result['limit']);
        $this->assertEquals(0, $result['offset']);
        $this->assertEquals(3, $result['total']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'https://api.sandbox.ebay.com/commerce/message/v1/conversation/c1234567890')
                && str_contains($request->url(), 'conversation_type=FROM_MEMBERS')
                && $request->hasHeader('Authorization', 'Bearer test_access_token');
        });
    }

    public function test_send_message_returns_message_object()
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Message/sendMessage.json'),
            true
        );

        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/send_message' => Http::response($fixture, 200),
        ]);

        $message = Ebay::message()->sendMessage([
            'otherPartyUsername' => 'buyer_username',
            'messageText' => 'Thank you for your question about this item.',
            'reference' => [
                'referenceId' => '123456789',
                'referenceType' => 'LISTING',
            ],
            'emailCopyToSender' => false,
        ]);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('m1111222233', $message->messageId);
        $this->assertEquals('Thank you for your question about this item.', $message->messageBody);
        $this->assertEquals('seller456', $message->senderUsername);
        $this->assertEquals('buyer_username', $message->recipientUsername);
        $this->assertFalse($message->readStatus);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $request->url() === 'https://api.sandbox.ebay.com/commerce/message/v1/send_message'
                && $request->method() === 'POST'
                && $body['otherPartyUsername'] === 'buyer_username'
                && $body['messageText'] === 'Thank you for your question about this item.'
                && $request->hasHeader('Authorization', 'Bearer test_access_token');
        });
    }

    public function test_send_message_reply_in_conversation()
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Message/sendMessage.json'),
            true
        );

        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/send_message' => Http::response($fixture, 200),
        ]);

        $message = Ebay::message()->sendMessage([
            'conversationId' => 'c1234567890',
            'messageText' => 'Here is the additional information you requested.',
        ]);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('m1111222233', $message->messageId);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $request->url() === 'https://api.sandbox.ebay.com/commerce/message/v1/send_message'
                && $body['conversationId'] === 'c1234567890'
                && $body['messageText'] === 'Here is the additional information you requested.';
        });
    }

    public function test_update_conversation_marks_as_read()
    {
        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/update_conversation' => Http::response('', 204),
        ]);

        Ebay::message()->updateConversation([
            'conversationId' => 'c1234567890',
            'conversationType' => ConversationType::FROM_MEMBERS->value,
            'read' => true,
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $request->url() === 'https://api.sandbox.ebay.com/commerce/message/v1/update_conversation'
                && $request->method() === 'POST'
                && $body['conversationId'] === 'c1234567890'
                && $body['conversationType'] === 'FROM_MEMBERS'
                && $body['read'] === true
                && $request->hasHeader('Authorization', 'Bearer test_access_token');
        });
    }

    public function test_update_conversation_archives_conversation()
    {
        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/update_conversation' => Http::response('', 204),
        ]);

        Ebay::message()->updateConversation([
            'conversationId' => 'c1234567890',
            'conversationType' => ConversationType::FROM_MEMBERS->value,
            'conversationStatus' => 'ARCHIVE',
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $body['conversationStatus'] === 'ARCHIVE';
        });
    }

    public function test_bulk_update_conversation_updates_multiple_conversations()
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Message/bulkUpdateConversation.json'),
            true
        );

        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/bulk_update_conversation' => Http::response($fixture, 200),
        ]);

        $result = Ebay::message()->bulkUpdateConversation([
            'conversations' => [
                [
                    'conversationId' => 'c1111111111',
                    'conversationType' => ConversationType::FROM_MEMBERS->value,
                    'conversationStatus' => 'ARCHIVE',
                ],
                [
                    'conversationId' => 'c2222222222',
                    'conversationType' => ConversationType::FROM_MEMBERS->value,
                    'conversationStatus' => 'ARCHIVE',
                ],
            ],
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('conversationsMetadata', $result);
        $this->assertEquals(2, $result['conversationsMetadata']['successCount']);
        $this->assertEquals(0, $result['conversationsMetadata']['failureCount']);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return $request->url() === 'https://api.sandbox.ebay.com/commerce/message/v1/bulk_update_conversation'
                && $request->method() === 'POST'
                && count($body['conversations']) === 2
                && $body['conversations'][0]['conversationId'] === 'c1111111111'
                && $body['conversations'][1]['conversationId'] === 'c2222222222'
                && $request->hasHeader('Authorization', 'Bearer test_access_token');
        });
    }

    public function test_api_error_throws_exception()
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Message/error.json'),
            true
        );

        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/conversation*' => Http::response($fixture, 400),
        ]);

        $this->expectException(EbayApiException::class);
        $this->expectExceptionMessage('Invalid conversation_type parameter');

        Ebay::message()->getConversations([
            'conversation_type' => 'INVALID_TYPE',
        ]);
    }

    public function test_missing_access_token_throws_exception()
    {
        $ebay = new \Tigusigalpa\Ebay\Ebay('sandbox');

        $this->expectException(EbayApiException::class);
        $this->expectExceptionMessage('Access token required but not available');

        $ebay->message()->getConversations([
            'conversation_type' => ConversationType::FROM_MEMBERS->value,
        ]);
    }

    public function test_conversation_with_pagination()
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Message/getConversations.json'),
            true
        );
        $fixture['next'] = 'https://api.ebay.com/commerce/message/v1/conversation?conversation_type=FROM_MEMBERS&limit=25&offset=25';
        $fixture['prev'] = null;

        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/conversation*' => Http::response($fixture, 200),
        ]);

        $result = Ebay::message()->getConversations([
            'conversation_type' => ConversationType::FROM_MEMBERS->value,
            'limit' => 25,
            'offset' => 0,
        ]);

        $this->assertNotNull($result['next']);
        $this->assertNull($result['prev']);
        $this->assertEquals(25, $result['limit']);
        $this->assertEquals(0, $result['offset']);
    }

    public function test_get_conversations_with_filters()
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Message/getConversations.json'),
            true
        );

        Http::fake([
            'https://api.sandbox.ebay.com/commerce/message/v1/conversation*' => Http::response($fixture, 200),
        ]);

        Ebay::message()->getConversations([
            'conversation_type' => ConversationType::FROM_MEMBERS->value,
            'conversation_status' => ConversationStatus::UNREAD->value,
            'other_party_username' => 'buyer123',
            'reference_id' => '123456789',
            'reference_type' => 'LISTING',
            'start_time' => '2024-01-01T00:00:00.000Z',
            'end_time' => '2024-01-31T23:59:59.999Z',
        ]);

        Http::assertSent(function ($request) {
            $url = $request->url();
            return str_contains($url, 'conversation_type=FROM_MEMBERS')
                && str_contains($url, 'conversation_status=UNREAD')
                && str_contains($url, 'other_party_username=buyer123')
                && str_contains($url, 'reference_id=123456789')
                && str_contains($url, 'reference_type=LISTING')
                && str_contains($url, 'start_time=2024-01-01T00%3A00%3A00.000Z')
                && str_contains($url, 'end_time=2024-01-31T23%3A59%3A59.999Z');
        });
    }
}
