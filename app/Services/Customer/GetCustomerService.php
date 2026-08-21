<?php

namespace App\Services\Customer;

use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerNotFoundException;
use App\Domain\Customer\CustomerRepositoryInterface;

class GetCustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    public function handle(int $id): Customer
    {
        return $this->customers->find($id) ?? throw new CustomerNotFoundException($id);
    }
}
