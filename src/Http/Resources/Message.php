<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources;

/**
 * Message Resource DTO
 */
class Message
{
    public function __construct(
        public readonly ?string $messageId,
        public readonly ?string $messageBody,
        public readonly ?string $createdDate,
        public readonly ?bool $readStatus,
        public readonly ?string $senderUsername,
        public readonly ?string $recipientUsername,
        public readonly ?string $subject,
        public readonly array $messageMedia = []
    ) {
    }

    public static function fromArray(array $data): self
    {
        $messageMedia = [];
        if (isset($data['messageMedia']) && is_array($data['messageMedia'])) {
            foreach ($data['messageMedia'] as $mediaData) {
                $messageMedia[] = MessageMedia::fromArray($mediaData);
            }
        }

        return new self(
            messageId: $data['messageId'] ?? null,
            messageBody: $data['messageBody'] ?? null,
            createdDate: $data['createdDate'] ?? null,
            readStatus: $data['readStatus'] ?? null,
            senderUsername: $data['senderUsername'] ?? $data['senderUserName'] ?? null,
            recipientUsername: $data['recipientUsername'] ?? $data['recipientUserName'] ?? null,
            subject: $data['subject'] ?? null,
            messageMedia: $messageMedia
        );
    }

    public function toArray(): array
    {
        return [
            'messageId' => $this->messageId,
            'messageBody' => $this->messageBody,
            'createdDate' => $this->createdDate,
            'readStatus' => $this->readStatus,
            'senderUsername' => $this->senderUsername,
            'recipientUsername' => $this->recipientUsername,
            'subject' => $this->subject,
            'messageMedia' => array_map(fn($media) => $media->toArray(), $this->messageMedia),
        ];
    }
}
