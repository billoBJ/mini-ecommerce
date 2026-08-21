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
     *
     * $changedBy: who to attribute this save's status to in
     * order_status_history. Deliberately NOT part of Order's own state —
     * it's about who performed *this* save, which can differ from the
     * order's assigned staff user (userId).
     */
    public function save(Order $order, ?int $changedBy = null): Order;

    // Deliberately no delete(): an order is never hard-deleted, it is
    // cancelled (a status transition via withStatus()).
}
