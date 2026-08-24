<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Order\Order as OrderEntity;
use App\Domain\Order\OrderItem as OrderItemEntity;
use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Order\OrderStatus;
use App\Models\Order as OrderModel;
use App\Models\OrderItem as OrderItemModel;
use Illuminate\Support\Facades\DB;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function all(): array
    {
        return OrderModel::with('items')
            ->orderBy('id')
            ->get()
            ->map(fn (OrderModel $model) => $this->toDomain($model))
            ->all();
    }

    public function find(int $id): ?OrderEntity
    {
        $model = OrderModel::with('items')->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function save(OrderEntity $order, ?int $changedBy = null): OrderEntity
    {
        // Everything below runs in ONE transaction: if inserting item #2
        // fails, item #1 and the order row are rolled back too — you can
        // never end up with an order that has zero, or partial, items.
        return DB::transaction(function () use ($order, $changedBy) {
            $model = $order->id
                ? $this->update($order, $changedBy)
                : $this->insert($order, $changedBy);

            return $this->toDomain($model->load('items'));
        });
    }

    private function insert(OrderEntity $order, ?int $changedBy): OrderModel
    {
        $model = OrderModel::create([
            'customer_id' => $order->customerId,
            'user_id' => $order->userId,
            'status' => $order->status->value,
            // The entity computes these from its items — the repository
            // never invents a total, it only asks the entity for one.
            'subtotal' => $order->subtotal(),
            'tax' => $order->tax(),
            'discount' => $order->discount(),
            'total' => $order->total(),
            'currency' => $order->currency,
            'notes' => $order->notes,
        ]);

        foreach ($order->items() as $item) {
            $model->items()->create([
                'product_id' => $item->productId,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'discount' => $item->discount,
                'total' => $item->total(),
            ]);
        }

        $this->recordStatusChange($model, $order->status, $changedBy);

        return $model;
    }

    private function update(OrderEntity $order, ?int $changedBy): OrderModel
    {
        $model = OrderModel::findOrFail($order->id);
        $statusChanged = $model->status !== $order->status->value;

        $model->update(['status' => $order->status->value]);

        // Only append to the history log if the status actually moved —
        // saving an order that didn't change status shouldn't create
        // phantom history entries.
        if ($statusChanged) {
            $this->recordStatusChange($model, $order->status, $changedBy);
        }

        return $model;
    }

    private function recordStatusChange(OrderModel $model, OrderStatus $status, ?int $changedBy): void
    {
        $model->statusHistory()->create([
            'status' => $status->value,
            'changed_by' => $changedBy,
        ]);
    }

    private function toDomain(OrderModel $model): OrderEntity
    {
        $items = $model->items->map(fn (OrderItemModel $item) => new OrderItemEntity(
            id: $item->id,
            productId: $item->product_id,
            name: $item->name,
            sku: $item->sku,
            price: (float) $item->price,
            quantity: $item->quantity,
            discount: (float) $item->discount,
        ))->all();

        return new OrderEntity(
            id: $model->id,
            customerId: $model->customer_id,
            userId: $model->user_id,
            status: OrderStatus::from($model->status),
            items: $items,
            discount: (float) $model->discount,
            currency: $model->currency,
            notes: $model->notes,
        );
    }
}
