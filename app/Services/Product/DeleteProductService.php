<?php

namespace App\Services\Product;

use App\Domain\Product\ProductNotFoundException;
use App\Domain\Product\ProductRepositoryInterface;

class DeleteProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function handle(int $id): void
    {
        if (! $this->products->find($id)) {
            throw new ProductNotFoundException($id);
        }

        $this->products->delete($id);
    }
}
