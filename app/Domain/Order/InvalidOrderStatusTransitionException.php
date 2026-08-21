<?php

namespace App\Domain\Order;

class InvalidOrderStatusTransitionException extends \DomainException
{
    public function __construct(OrderStatus $from, OrderStatus $to)
    {
        parent::__construct("Cannot transition order from [{$from->value}] to [{$to->value}].");
    }
}
