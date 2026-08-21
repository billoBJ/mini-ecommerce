<?php

namespace App\DTOs\Customer;

class CreateCustomerDTO
{
    public function __construct(
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

    /**
     * Build the DTO from a snake_case request payload
     * (validated() keys match the DB column names, not the DTO's
     * camelCase properties, so the mapping happens once, here).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            addressLine1: $data['address_line_1'] ?? null,
            addressLine2: $data['address_line_2'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            country: $data['country'],
        );
    }
}
