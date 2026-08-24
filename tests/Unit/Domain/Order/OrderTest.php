<?php

namespace Tests\Unit\Domain\Order;

use App\Domain\Order\EmptyOrderException;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\OrderStatus;
use PHPUnit\Framework\TestCase;
use App\Domain\Order\InvalidOrderStatusTransitionException;

class OrderTest extends TestCase
{
    private function makeItem(float $price, int $quantity, float $discount = 0): OrderItem
    {
        return new OrderItem(
            id: null,
            productId: 1,
            name: 'Producto de prueba',
            sku: 'TEST-SKU',
            price: $price,
            quantity: $quantity,
            discount: $discount,
        );
    }

    public function test_it_starts_a_new_order_as_pending_with_no_id(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(10, 1)]);

        $this->assertNull($order->id);
        $this->assertSame(OrderStatus::Pending, $order->status);
    }

    public function test_it_rejects_an_order_with_zero_items(): void
    {
        $this->expectException(EmptyOrderException::class);
        $this->expectExceptionMessage('An order must contain at least one item.');

        Order::place(customerId: 1, userId: null, items: []);
    }
    public function test_it_computes_subtotal_as_the_sum_of_each_item_total(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [
            $this->makeItem(price: 100, quantity: 2),          // 200
            $this->makeItem(price: 50, quantity: 1, discount: 5), // 45
        ]);

        $this->assertSame(245.0, $order->subtotal());
    }

    public function test_it_computes_tax_as_16_percent_of_the_subtotal(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(price: 100, quantity: 1)]);

        $this->assertSame(16.0, $order->tax());
    }

    public function test_it_computes_total_as_subtotal_plus_tax_minus_the_order_discount(): void
    {
        $order = Order::place(
            customerId: 1,
            userId: null,
            items: [$this->makeItem(price: 100, quantity: 1)],
            discount: 10,
        );

        // subtotal 100 + tax 16 - discount 10
        $this->assertSame(106.0, $order->total());
    }
    public function test_it_walks_the_full_happy_path_pending_to_completed(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(10, 1)]);

        $order = $order->withStatus(OrderStatus::Confirmed);
        $this->assertSame(OrderStatus::Confirmed, $order->status);

        $order = $order->withStatus(OrderStatus::Processing);
        $this->assertSame(OrderStatus::Processing, $order->status);

        $order = $order->withStatus(OrderStatus::Shipped);
        $this->assertSame(OrderStatus::Shipped, $order->status);

        $order = $order->withStatus(OrderStatus::Completed);
        $this->assertSame(OrderStatus::Completed, $order->status);
    }

    public function test_it_allows_cancelling_from_pending(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(10, 1)]);

        $cancelled = $order->withStatus(OrderStatus::Cancelled);

        $this->assertSame(OrderStatus::Cancelled, $cancelled->status);
    }

    public function test_it_allows_cancelling_from_confirmed(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(10, 1)])
            ->withStatus(OrderStatus::Confirmed);

        $cancelled = $order->withStatus(OrderStatus::Cancelled);

        $this->assertSame(OrderStatus::Cancelled, $cancelled->status);
    }

    public function test_it_does_not_mutate_the_original_order_with_status_returns_a_new_instance(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(10, 1)]);

        $confirmed = $order->withStatus(OrderStatus::Confirmed);

        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame(OrderStatus::Confirmed, $confirmed->status);
        $this->assertNotSame($order, $confirmed);
    }

    public function test_it_rejects_skipping_a_step_confirmed_cannot_jump_to_shipped(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(10, 1)])
            ->withStatus(OrderStatus::Confirmed);

        $this->expectException(InvalidOrderStatusTransitionException::class);
        $this->expectExceptionMessage('Cannot transition order from [confirmed] to [shipped].');

        $order->withStatus(OrderStatus::Shipped);
    }

    public function test_it_rejects_any_transition_once_an_order_is_completed(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(10, 1)])
            ->withStatus(OrderStatus::Confirmed)
            ->withStatus(OrderStatus::Processing)
            ->withStatus(OrderStatus::Shipped)
            ->withStatus(OrderStatus::Completed);

        $this->expectException(InvalidOrderStatusTransitionException::class);

        $order->withStatus(OrderStatus::Cancelled);
    }

    public function test_it_rejects_cancelling_an_order_that_is_already_shipped(): void
    {
        $order = Order::place(customerId: 1, userId: null, items: [$this->makeItem(10, 1)])
            ->withStatus(OrderStatus::Confirmed)
            ->withStatus(OrderStatus::Processing)
            ->withStatus(OrderStatus::Shipped);

        $this->expectException(InvalidOrderStatusTransitionException::class);

        $order->withStatus(OrderStatus::Cancelled);
    }


}