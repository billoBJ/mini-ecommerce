<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Customer\Customer as CustomerEntity;
use App\Domain\Customer\CustomerRepositoryInterface;
use App\Models\Customer as CustomerModel;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function all(): array
    {
        return CustomerModel::query()
            ->get()
            ->map(fn (CustomerModel $model) => $this->toDomain($model))
            ->all();
    }

    public function find(int $id): ?CustomerEntity
    {
        $model = CustomerModel::query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function save(CustomerEntity $customer): CustomerEntity
    {
        $model = $customer->id
            ? CustomerModel::query()->findOrFail($customer->id)
            : new CustomerModel();

        $model->fill([
            'user_id' => $customer->userId,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address_line_1' => $customer->addressLine1,
            'address_line_2' => $customer->addressLine2,
            'city' => $customer->city,
            'state' => $customer->state,
            'postal_code' => $customer->postalCode,
            'country' => $customer->country,
        ]);

        $model->save();

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        CustomerModel::destroy($id);
    }

    private function toDomain(CustomerModel $model): CustomerEntity
    {
        return new CustomerEntity(
            id: $model->id,
            userId: $model->user_id,
            name: $model->name,
            email: $model->email,
            phone: $model->phone,
            addressLine1: $model->address_line_1,
            addressLine2: $model->address_line_2,
            city: $model->city,
            state: $model->state,
            postalCode: $model->postal_code,
            country: $model->country,
        );
    }
}
