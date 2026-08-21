<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Services\Customer\ListCustomersService;

class ListCustomerController extends Controller
{
    public function __invoke(ListCustomersService $listCustomers)
    {
        return CustomerResource::collection($listCustomers->handle());
    }
}
