<?php

namespace App\Domain\Order;

class OrderNotFoundException extends \DomainException
{
    public function __construct(public readonly int $id)
    {
        parent::__construct(__('messages.errors.order_not_found', ['id' => $id]));
    }
}
