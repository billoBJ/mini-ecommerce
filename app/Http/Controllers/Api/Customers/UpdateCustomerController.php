<?php

namespace App\Http\Controllers\Api\Customers;

use App\DTOs\Customer\UpdateCustomerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\Customer\UpdateCustomerService;

class UpdateCustomerController extends Controller
{
    public function __invoke(int $customer, UpdateCustomerRequest $request, UpdateCustomerService $updateCustomer)
    {
        $dto = UpdateCustomerDTO::fromArray($customer, $request->validated());

        return $this->withMessage(
            new CustomerResource($updateCustomer->handle($dto)),
            'messages.success.customer_updated',
        );
    }
}
