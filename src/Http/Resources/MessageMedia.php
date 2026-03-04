<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources;

use Tigusigalpa\Ebay\Enums\MessageMediaType;

/**
 * Message Media Resource DTO
 */
class MessageMedia
{
    public function __construct(
        public readonly ?string $mediaName,
        public readonly ?MessageMediaType $mediaType,
        public readonly ?string $mediaUrl
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            mediaName: $data['mediaName'] ?? null,
            mediaType: isset($data['mediaType']) 
                ? MessageMediaType::tryFrom($data['mediaType']) 
                : null,
            mediaUrl: $data['mediaUrl'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'mediaName' => $this->mediaName,
            'mediaType' => $this->mediaType?->value,
            'mediaUrl' => $this->mediaUrl,
        ];
    }
}
