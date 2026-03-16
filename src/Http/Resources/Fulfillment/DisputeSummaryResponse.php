<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * DisputeSummaryResponse DTO
 * 
 * Represents a paginated collection of payment dispute summaries
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/getPaymentDisputeSummaries
 */
class DisputeSummaryResponse
{
    public function __construct(
        public readonly ?string $href,
        public readonly ?int $total,
        public readonly ?string $next,
        public readonly ?string $prev,
        public readonly ?int $limit,
        public readonly ?int $offset,
        public readonly array $paymentDisputeSummaries
    ) {
    }

    /**
     * Create DisputeSummaryResponse from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $summaries = [];
        if (isset($data['paymentDisputeSummaries']) && is_array($data['paymentDisputeSummaries'])) {
            $summaries = array_map(fn($summary) => PaymentDisputeSummary::fromArray($summary), $data['paymentDisputeSummaries']);
        }

        return new self(
            href: $data['href'] ?? null,
            total: $data['total'] ?? null,
            next: $data['next'] ?? null,
            prev: $data['prev'] ?? null,
            limit: $data['limit'] ?? null,
            offset: $data['offset'] ?? null,
            paymentDisputeSummaries: $summaries
        );
    }
}
