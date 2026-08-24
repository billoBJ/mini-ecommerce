<?php

namespace App\Domain\Order;

class EmptyOrderException extends \DomainException
{
    public function __construct()
    {
        parent::__construct(__('messages.errors.empty_order'));
    }
}
