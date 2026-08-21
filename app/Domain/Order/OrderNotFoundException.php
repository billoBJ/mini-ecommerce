<?php

namespace App\Domain\Order;

class OrderNotFoundException extends \DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Order [{$id}] not found.");
    }
}
