<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

use Tigusigalpa\Ebay\Enums\EvidenceTypeEnum;

/**
 * DisputeEvidence DTO
 * 
 * Represents evidence in a payment dispute
 */
class DisputeEvidence
{
    public function __construct(
        public readonly ?string $evidenceId,
        public readonly ?EvidenceTypeEnum $evidenceType,
        public readonly ?array $files,
        public readonly ?array $lineItems,
        public readonly ?string $providedDate,
        public readonly ?string $requestDate
    ) {
    }

    /**
     * Create DisputeEvidence from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $files = null;
        if (isset($data['files']) && is_array($data['files'])) {
            $files = array_map(fn($file) => FileEvidence::fromArray($file), $data['files']);
        }

        return new self(
            evidenceId: $data['evidenceId'] ?? null,
            evidenceType: isset($data['evidenceType']) ? EvidenceTypeEnum::tryFrom($data['evidenceType']) : null,
            files: $files,
            lineItems: $data['lineItems'] ?? null,
            providedDate: $data['providedDate'] ?? null,
            requestDate: $data['requestDate'] ?? null
        );
    }
}
