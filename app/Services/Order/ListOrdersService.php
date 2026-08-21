<?php

namespace App\Services\Order;

use App\Domain\Order\OrderRepositoryInterface;

class ListOrdersService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {}

    /**
     * @return \App\Domain\Order\Order[]
     */
    public function handle(): array
    {
        return $this->orders->all();
    }
}
