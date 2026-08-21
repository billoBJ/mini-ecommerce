<?php

namespace App\Services\Customer;

use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\DTOs\Customer\CreateCustomerDTO;

class CreateCustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    public function handle(CreateCustomerDTO $dto): Customer
    {
        $customer = new Customer(
            id: null,
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
