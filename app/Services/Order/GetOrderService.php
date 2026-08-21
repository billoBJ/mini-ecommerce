<?php

namespace App\Services\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderNotFoundException;
use App\Domain\Order\OrderRepositoryInterface;

class GetOrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {}

    public function handle(int $id): Order
    {
        return $this->orders->find($id) ?? throw new OrderNotFoundException($id);
    }
}
