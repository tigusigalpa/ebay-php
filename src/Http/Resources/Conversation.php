<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources;

use Tigusigalpa\Ebay\Enums\ConversationStatus;
use Tigusigalpa\Ebay\Enums\ConversationType;

/**
 * Conversation Resource DTO
 */
class Conversation
{
    public function __construct(
        public readonly ?string $conversationId,
        public readonly ?ConversationStatus $conversationStatus,
        public readonly ?string $conversationTitle,
        public readonly ?ConversationType $conversationType,
        public readonly ?string $createdDate,
        public readonly ?Message $latestMessage,
        public readonly ?string $referenceId,
        public readonly ?string $referenceType,
        public readonly ?int $unreadCount
    ) {
    }

    public static function fromArray(array $data): self
    {
        $latestMessage = null;
        if (isset($data['latestMessage']) && is_array($data['latestMessage'])) {
            $latestMessage = Message::fromArray($data['latestMessage']);
        }

        return new self(
            conversationId: $data['conversationId'] ?? null,
            conversationStatus: isset($data['conversationStatus']) 
                ? ConversationStatus::tryFrom($data['conversationStatus']) 
                : null,
            conversationTitle: $data['conversationTitle'] ?? null,
            conversationType: isset($data['conversationType']) 
                ? ConversationType::tryFrom($data['conversationType']) 
                : null,
            createdDate: $data['createdDate'] ?? null,
            latestMessage: $latestMessage,
            referenceId: $data['referenceId'] ?? null,
            referenceType: $data['referenceType'] ?? null,
            unreadCount: isset($data['unreadCount']) ? (int) $data['unreadCount'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'conversationId' => $this->conversationId,
            'conversationStatus' => $this->conversationStatus?->value,
            'conversationTitle' => $this->conversationTitle,
            'conversationType' => $this->conversationType?->value,
            'createdDate' => $this->createdDate,
            'latestMessage' => $this->latestMessage?->toArray(),
            'referenceId' => $this->referenceId,
            'referenceType' => $this->referenceType,
            'unreadCount' => $this->unreadCount,
        ];
    }
}
