<?php

namespace App\Services\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use App\DTOs\Product\CreateProductDTO;

class CreateProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function handle(CreateProductDTO $dto): Product
    {
        $product = new Product(
            id: null,
            name: $dto->name,
            sku: $dto->sku,
            description: $dto->description,
            price: $dto->price,
            cost: $dto->cost,
            stock: $dto->stock,
        );

        return $this->products->save($product);
    }
}
