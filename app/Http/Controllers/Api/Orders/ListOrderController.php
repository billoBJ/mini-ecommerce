<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Services\Order\ListOrdersService;

class ListOrderController extends Controller
{
    public function __invoke(ListOrdersService $listOrders)
    {
        return OrderResource::collection($listOrders->handle());
    }
}
