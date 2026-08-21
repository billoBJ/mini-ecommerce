<?php

namespace App\Domain\Product;

class Product
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $sku,
        public readonly ?string $description,
        public readonly float $price,
        public readonly ?float $cost,
        public readonly int $stock,
        public readonly bool $active = true,
    ) {}
}
