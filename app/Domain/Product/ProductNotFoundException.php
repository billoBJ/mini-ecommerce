<?php

namespace App\Domain\Product;

class ProductNotFoundException extends \DomainException
{
    public function __construct(public readonly int $id)
    {
        parent::__construct(__('messages.errors.product_not_found', ['id' => $id]));
    }
}
