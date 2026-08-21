<?php

namespace App\Services\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductNotFoundException;
use App\Domain\Product\ProductRepositoryInterface;
use App\DTOs\Product\UpdateProductDTO;

class UpdateProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function handle(UpdateProductDTO $dto): Product
    {
        if (! $this->products->find($dto->id)) {
            throw new ProductNotFoundException($dto->id);
        }

        $product = new Product(
            id: $dto->id,
            name: $dto->name,
            sku: $dto->sku,
            description: $dto->description,
            price: $dto->price,
            cost: $dto->cost,
            stock: $dto->stock,
            active: $dto->active,
        );

        return $this->products->save($product);
    }
}
