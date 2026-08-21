<?php

namespace App\DTOs\Product;

class UpdateProductDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $sku,
        public readonly float $price,
        public readonly int $stock,
        public readonly ?string $description = null,
        public readonly ?float $cost = null,
        public readonly bool $active = true,
    ) {}
}
