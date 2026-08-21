<?php

namespace App\Services\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Product\InsufficientStockException;
use App\Domain\Product\ProductNotFoundException;
use App\Domain\Product\ProductRepositoryInterface;
use App\DTOs\Order\CreateOrderDTO;
use App\DTOs\Order\OrderItemInputDTO;

class CreateOrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function handle(CreateOrderDTO $dto): Order
    {
        $items = array_map(
            fn (OrderItemInputDTO $input) => $this->resolveItem($input),
            $dto->items,
        );

        $order = Order::place(
            customerId: $dto->customerId,
            userId: $dto->userId,
            items: $items,
            discount: $dto->discount,
            currency: $dto->currency,
            notes: $dto->notes,
        );

        return $this->orders->save($order, changedBy: $dto->userId);
    }

    /**
     * The one place in the whole system where a product's current
     * name/sku/price gets frozen into a permanent snapshot.
     */
    private function resolveItem(OrderItemInputDTO $input): OrderItem
    {
        $product = $this->products->find($input->productId)
            ?? throw new ProductNotFoundException($input->productId);

        if ($product->stock < $input->quantity) {
            throw new InsufficientStockException($product->id, $input->quantity, $product->stock);
        }

        return new OrderItem(
            id: null,
            productId: $product->id,
            name: $product->name,
            sku: $product->sku,
            price: $product->price,
            quantity: $input->quantity,
            discount: $input->discount,
        );
    }
}
