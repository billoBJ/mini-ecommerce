<?php

namespace App\Services\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductNotFoundException;
use App\Domain\Product\ProductRepositoryInterface;

class GetProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function handle(int $id): Product
    {
        return $this->products->find($id) ?? throw new ProductNotFoundException($id);
    }
}
