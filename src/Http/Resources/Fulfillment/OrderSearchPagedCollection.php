<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Resources\Fulfillment;

/**
 * OrderSearchPagedCollection DTO
 * 
 * Represents a paginated collection of orders
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/resources/order/methods/getOrders
 */
class OrderSearchPagedCollection
{
    public function __construct(
        public readonly ?string $href,
        public readonly ?int $total,
        public readonly ?string $next,
        public readonly ?string $prev,
        public readonly ?int $limit,
        public readonly ?int $offset,
        public readonly array $orders,
        public readonly ?array $warnings
    ) {
    }

    /**
     * Create OrderSearchPagedCollection from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $orders = [];
        if (isset($data['orders']) && is_array($data['orders'])) {
            $orders = array_map(fn($order) => Order::fromArray($order), $data['orders']);
        }

        return new self(
            href: $data['href'] ?? null,
            total: $data['total'] ?? null,
            next: $data['next'] ?? null,
            prev: $data['prev'] ?? null,
            limit: $data['limit'] ?? null,
            offset: $data['offset'] ?? null,
            orders: $orders,
            warnings: $data['warnings'] ?? null
        );
    }
}
