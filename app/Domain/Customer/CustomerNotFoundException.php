<?php

namespace App\Domain\Customer;

class CustomerNotFoundException extends \DomainException
{
    public function __construct(int $id)
    {
        parent::__construct("Customer [{$id}] not found.");
    }
}
