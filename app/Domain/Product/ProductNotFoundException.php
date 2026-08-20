<?php

namespace App\Domain\Product;

class ProductNotFoundException extends \DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Product [{$id}] not found.");
    }
}
