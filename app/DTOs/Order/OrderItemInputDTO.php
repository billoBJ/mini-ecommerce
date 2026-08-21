<?php

namespace App\DTOs\Order;

class OrderItemInputDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $quantity,
        public readonly float $discount = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            quantity: $data['quantity'],
            discount: $data['discount'] ?? 0,
        );
    }
}
