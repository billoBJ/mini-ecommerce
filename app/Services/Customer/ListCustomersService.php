<?php

namespace App\Services\Customer;

use App\Domain\Customer\CustomerRepositoryInterface;

class ListCustomersService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    /**
     * @return \App\Domain\Customer\Customer[]
     */
    public function handle(): array
    {
        return $this->customers->all();
    }
}
