<?php

namespace App\Domain\Order;

class Order
{
    private const TAX_RATE = 0.16;

    /**
     * @param OrderItem[] $items
     */
    public function __construct(
        public readonly ?int $id,
        public readonly int $customerId,
        public readonly ?int $userId,
        public readonly OrderStatus $status,
        private readonly array $items,
        private readonly float $discount = 0,
        public readonly string $currency = 'USD',
        public readonly ?string $notes = null,
    ) {
        if ($this->items === []) {
            throw new EmptyOrderException();
        }
    }

    /**
     * Intention-revealing shortcut for the common case: starting a brand
     * new order always begins at Pending with no id yet.
     *
     * @param OrderItem[] $items
     */
    public static function place(
        int $customerId,
        ?int $userId,
        array $items,
        float $discount = 0,
        string $currency = 'USD',
        ?string $notes = null,
    ): self {
        return new self(
            id: null,
            customerId: $customerId,
            userId: $userId,
            status: OrderStatus::Pending,
            items: $items,
            discount: $discount,
            currency: $currency,
            notes: $notes,
        );
    }

    /**
     * @return OrderItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function subtotal(): float
    {
        return round(array_sum(array_map(
            fn (OrderItem $item) => $item->total(),
            $this->items,
        )), 2);
    }

    public function tax(): float
    {
        return round($this->subtotal() * self::TAX_RATE, 2);
    }

    public function discount(): float
    {
        return $this->discount;
    }

    public function total(): float
    {
        return round($this->subtotal() + $this->tax() - $this->discount, 2);
    }

    /**
     * Returns a NEW Order in the next status — same immutable "wither"
     * pattern used everywhere else in this codebase (Product, Customer):
     * nothing mutates in place, a fresh snapshot is handed to the
     * repository to persist.
     */
    public function withStatus(OrderStatus $next): self
    {
        if (! in_array($next, $this->allowedTransitions(), true)) {
            throw new InvalidOrderStatusTransitionException($this->status, $next);
        }

        return new self(
            id: $this->id,
            customerId: $this->customerId,
            userId: $this->userId,
            status: $next,
            items: $this->items,
            discount: $this->discount,
            currency: $this->currency,
            notes: $this->notes,
        );
    }

    /**
     * @return OrderStatus[]
     */
    private function allowedTransitions(): array
    {
        return match ($this->status) {
            OrderStatus::Pending => [OrderStatus::Confirmed, OrderStatus::Cancelled],
            OrderStatus::Confirmed => [OrderStatus::Processing, OrderStatus::Cancelled],
            OrderStatus::Processing => [OrderStatus::Shipped],
            OrderStatus::Shipped => [OrderStatus::Completed],
            OrderStatus::Completed, OrderStatus::Cancelled => [],
        };
    }
}
