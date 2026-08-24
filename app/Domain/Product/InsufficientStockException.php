<?php

namespace App\Domain\Product;

class InsufficientStockException extends \DomainException
{
    public function __construct(
        public readonly int $productId,
        public readonly int $requested,
        public readonly int $available,
    ) {
        parent::__construct(__('messages.errors.insufficient_stock', [
            'id' => $productId,
            'requested' => $requested,
            'available' => $available,
        ]));
    }
}
