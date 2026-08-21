<?php

namespace App\DTOs\Order;

class CreateOrderDTO
{
    /**
     * @param OrderItemInputDTO[] $items
     */
    public function __construct(
        public readonly int $customerId,
        public readonly ?int $userId,
        public readonly array $items,
        public readonly float $discount = 0,
        public readonly string $currency = 'USD',
        public readonly ?string $notes = null,
    ) {}

    /**
     * $userId is taken separately (not from $data) on purpose: it comes
     * from the authenticated staff member making the request, never from
     * client-submitted JSON — otherwise anyone could attribute an order
     * to a different user.
     */
    public static function fromArray(array $data, ?int $userId): self
    {
        return new self(
            customerId: $data['customer_id'],
            userId: $userId,
            items: array_map(
                fn (array $item) => OrderItemInputDTO::fromArray($item),
                $data['items'],
            ),
            discount: $data['discount'] ?? 0,
            currency: $data['currency'] ?? 'USD',
            notes: $data['notes'] ?? null,
        );
    }
}
