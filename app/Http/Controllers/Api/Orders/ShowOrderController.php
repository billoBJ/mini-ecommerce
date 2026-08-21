<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Services\Order\GetOrderService;

class ShowOrderController extends Controller
{
    public function __invoke(int $order, GetOrderService $getOrder)
    {
        return new OrderResource($getOrder->handle($order));
    }
}
