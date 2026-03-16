<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * AddEvidencePaymentDisputeResponse DTO
 * 
 * Represents the response from adding evidence to a payment dispute
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/addEvidence
 */
class AddEvidencePaymentDisputeResponse
{
    public function __construct(
        public readonly ?string $evidenceId
    ) {
    }

    /**
     * Create AddEvidencePaymentDisputeResponse from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            evidenceId: $data['evidenceId'] ?? null
        );
    }
}
