<?php

namespace App\Domain\Customer;

class CustomerNotFoundException extends \DomainException
{
    public function __construct(public readonly int $id)
    {
        parent::__construct(__('messages.errors.customer_not_found', ['id' => $id]));
    }
}
