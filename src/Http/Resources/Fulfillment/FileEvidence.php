<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * FileEvidence DTO
 * 
 * Represents a file evidence
 */
class FileEvidence
{
    public function __construct(
        public readonly ?string $fileId,
        public readonly ?string $name,
        public readonly ?int $size,
        public readonly ?string $uploadedDate
    ) {
    }

    /**
     * Create FileEvidence from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fileId: $data['fileId'] ?? null,
            name: $data['name'] ?? null,
            size: $data['size'] ?? null,
            uploadedDate: $data['uploadedDate'] ?? null
        );
    }
}
