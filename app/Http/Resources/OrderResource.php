<?php

namespace App\Http\Resources;

use App\Domain\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Order $order */
        $order = $this->resource;

        return [
            'id' => $order->id,
            'customer_id' => $order->customerId,
            'user_id' => $order->userId,
            'status' => $order->status->value,
            'subtotal' => $order->subtotal(),
            'tax' => $order->tax(),
            'discount' => $order->discount(),
            'total' => $order->total(),
            'currency' => $order->currency,
            'notes' => $order->notes,
            'items' => OrderItemResource::collection($order->items()),
        ];
    }
}
