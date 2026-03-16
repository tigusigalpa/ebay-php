<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * DisputeActivity DTO
 * 
 * Represents a dispute activity
 */
class DisputeActivity
{
    public function __construct(
        public readonly ?string $activityType,
        public readonly ?string $activityDate,
        public readonly ?string $activityBy,
        public readonly ?string $note
    ) {
    }

    /**
     * Create DisputeActivity from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            activityType: $data['activityType'] ?? null,
            activityDate: $data['activityDate'] ?? null,
            activityBy: $data['activityBy'] ?? null,
            note: $data['note'] ?? null
        );
    }
}
