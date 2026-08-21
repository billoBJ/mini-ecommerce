<?php

namespace App\Services\Customer;

use App\Domain\Customer\CustomerNotFoundException;
use App\Domain\Customer\CustomerRepositoryInterface;

class DeleteCustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    public function handle(int $id): void
    {
        if (! $this->customers->find($id)) {
            throw new CustomerNotFoundException($id);
        }

        $this->customers->delete($id);
    }
}
