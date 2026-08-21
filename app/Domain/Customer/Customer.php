<?php

namespace App\Domain\Customer;

class Customer
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $userId,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $addressLine1,
        public readonly ?string $addressLine2,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $postalCode,
        public readonly string $country,
    ) {}
}
