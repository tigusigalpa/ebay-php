<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * PaymentDisputeActivityHistory DTO
 * 
 * Represents payment dispute activity history
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/payment_dispute/methods/getActivities
 */
class PaymentDisputeActivityHistory
{
    public function __construct(
        public readonly array $activities
    ) {
    }

    /**
     * Create PaymentDisputeActivityHistory from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $activities = [];
        if (isset($data['activity']) && is_array($data['activity'])) {
            $activities = array_map(fn($activity) => DisputeActivity::fromArray($activity), $data['activity']);
        }

        return new self(
            activities: $activities
        );
    }
}
