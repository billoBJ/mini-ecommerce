<?php

namespace App\Services\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Order\OrderStatus;

class TransitionOrderStatusService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly GetOrderService $getOrder,
    ) {}

    public function handle(int $orderId, OrderStatus $next, ?int $changedBy): Order
    {
        // Throws OrderNotFoundException if missing.
        $order = $this->getOrder->handle($orderId);

        // Throws InvalidOrderStatusTransitionException if the move
        // isn't allowed from the order's current status.
        $transitioned = $order->withStatus($next);

        return $this->orders->save($transitioned, changedBy: $changedBy);
    }
}
