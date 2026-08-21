<?php

namespace App\Domain\Customer;

interface CustomerRepositoryInterface
{
    /**
     * @return Customer[]
     */
    public function all(): array;

    public function find(int $id): ?Customer;

    public function save(Customer $customer): Customer;

    public function delete(int $id): void;
}
