<?php

namespace App\Http\Controllers\Api\Orders;

use App\DTOs\Order\CreateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\Order\CreateOrderService;

class StoreOrderController extends Controller
{
    public function __invoke(StoreOrderRequest $request, CreateOrderService $createOrder)
    {
        $dto = CreateOrderDTO::fromArray($request->validated(), userId: $request->user()->id);

        $order = $createOrder->handle($dto);

        return $this->withMessage(new OrderResource($order), 'messages.success.order_created')
            ->response()
            ->setStatusCode(201);
    }
}
