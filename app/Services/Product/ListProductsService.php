<?php

namespace App\Services\Product;

use App\Domain\Product\ProductRepositoryInterface;

class ListProductsService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    /**
     * @return \App\Domain\Product\Product[]
     */
    public function handle(): array
    {
        return $this->products->all();
    }
}
