<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * UploadEvidenceFileResponse DTO
 * 
 * Represents the response from uploading an evidence file
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/uploadEvidenceFile
 */
class UploadEvidenceFileResponse
{
    public function __construct(
        public readonly ?string $fileId
    ) {
    }

    /**
     * Create UploadEvidenceFileResponse from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fileId: $data['fileId'] ?? null
        );
    }
}
