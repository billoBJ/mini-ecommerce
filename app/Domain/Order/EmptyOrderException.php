<?php

namespace App\Domain\Order;

class EmptyOrderException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('An order must contain at least one item.');
    }
}
