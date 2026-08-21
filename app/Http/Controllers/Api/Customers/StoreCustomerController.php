<?php

namespace App\Http\Controllers\Api\Customers;

use App\DTOs\Customer\CreateCustomerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\Customer\CreateCustomerService;

class StoreCustomerController extends Controller
{
    public function __invoke(StoreCustomerRequest $request, CreateCustomerService $createCustomer)
    {
        $dto = CreateCustomerDTO::fromArray($request->validated());

        $customer = $createCustomer->handle($dto);

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }
}
