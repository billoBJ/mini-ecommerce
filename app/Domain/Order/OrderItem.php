<?php

namespace App\Domain\Order;

class OrderItem
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $productId,
        public readonly string $name,
        public readonly string $sku,
        public readonly float $price,
        public readonly int $quantity,
        public readonly float $discount = 0,
    ) {
        if ($this->quantity < 1) {
            throw new \InvalidArgumentException(__('messages.errors.order_item_quantity'));
        }
    }

    /**
     * Line total. Computed, never stored on this object — the same
     * principle as Order::total(): derive it from price/quantity/discount
     * instead of trusting a value handed in from outside.
     */
    public function total(): float
    {
        return round(($this->price * $this->quantity) - $this->discount, 2);
    }
}
