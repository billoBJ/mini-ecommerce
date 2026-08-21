<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Services\Customer\GetCustomerService;

class ShowCustomerController extends Controller
{
    public function __invoke(int $customer, GetCustomerService $getCustomer)
    {
        return new CustomerResource($getCustomer->handle($customer));
    }
}
