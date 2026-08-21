<?php

namespace App\Domain\Order;

interface OrderRepositoryInterface
{
    /**
     * @return Order[]
     */
    public function all(): array;

    public function find(int $id): ?Order;

    /**
     * Persists the order AND its items. No separate method for items —
     * they are part of the same aggregate and are always saved together.
     */
    public function save(Order $order): Order;

    // Deliberately no delete(): an order is never hard-deleted, it is
    // cancelled (a status transition via withStatus()).
}
