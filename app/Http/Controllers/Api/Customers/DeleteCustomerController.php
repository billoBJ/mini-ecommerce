<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Services\Customer\DeleteCustomerService;

class DeleteCustomerController extends Controller
{
    public function __invoke(int $customer, DeleteCustomerService $deleteCustomer)
    {
        $deleteCustomer->handle($customer);

        return response()->noContent();
    }
}
