<?php

namespace App\Domain\Product;

interface ProductRepositoryInterface
{
    /**
     * @return Product[]
     */
    public function all(): array;

    public function find(int $id): ?Product;

    public function save(Product $product): Product;

    public function delete(int $id): void;
}
