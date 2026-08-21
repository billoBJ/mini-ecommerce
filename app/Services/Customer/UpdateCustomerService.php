<?php

namespace App\Services\Customer;

use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerNotFoundException;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\DTOs\Customer\UpdateCustomerDTO;

class UpdateCustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    public function handle(UpdateCustomerDTO $dto): Customer
    {
        if (! $this->customers->find($dto->id)) {
            throw new CustomerNotFoundException($dto->id);
        }

        $customer = new Customer(
            id: $dto->id,
            userId: $dto->userId,
            name: $dto->name,
            email: $dto->email,
            phone: $dto->phone,
            addressLine1: $dto->addressLine1,
            addressLine2: $dto->addressLine2,
            city: $dto->city,
            state: $dto->state,
            postalCode: $dto->postalCode,
            country: $dto->country,
        );

        return $this->customers->save($customer);
    }
}
