<?php

namespace App\Http\Resources;

use App\Domain\Order\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var OrderItem $item */
        $item = $this->resource;

        return [
            'id' => $item->id,
            'product_id' => $item->productId,
            'name' => $item->name,
            'sku' => $item->sku,
            'price' => $item->price,
            'quantity' => $item->quantity,
            'discount' => $item->discount,
            'total' => $item->total(),
        ];
    }
}
