<?php

namespace App\Http\Controllers\Api\Orders;

use App\Domain\Order\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\Order\TransitionOrderStatusService;

class UpdateOrderStatusController extends Controller
{
    public function __invoke(int $order, UpdateOrderStatusRequest $request, TransitionOrderStatusService $transitionOrderStatus)
    {
        $next = OrderStatus::from($request->validated('status'));

        $updated = $transitionOrderStatus->handle($order, $next, changedBy: $request->user()->id);

        return $this->withMessage(
            new OrderResource($updated),
            'messages.success.order_status_updated',
        );
    }
}
