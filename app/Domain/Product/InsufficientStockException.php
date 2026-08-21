<?php

namespace App\Domain\Product;

class InsufficientStockException extends \DomainException
{
    public function __construct(int $productId, int $requested, int $available)
    {
        parent::__construct(
            "Product [{$productId}] has insufficient stock: requested {$requested}, available {$available}."
        );
    }
}
