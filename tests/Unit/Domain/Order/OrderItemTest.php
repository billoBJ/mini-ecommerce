<?php

namespace Tests\Unit\Domain\Order;

use App\Domain\Order\OrderItem;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    public function test_it_computes_line_total_as_price_times_quantity_minus_discount(): void
    {
        $item = new OrderItem(
            id: null,
            productId: 1,
            name: 'Teclado mecanico',
            sku: 'KEY-001',
            price: 100.0,
            quantity: 2,
            discount: 15.0,
        );

        $this->assertSame(185.0, $item->total());
    }

    public function test_it_defaults_discount_to_zero_when_not_given(): void
    {
        $item = new OrderItem(
            id: null,
            productId: 1,
            name: 'Mouse',
            sku: 'MOU-001',
            price: 50.0,
            quantity: 3,
        );

        $this->assertSame(150.0, $item->total());
    }

    public function test_it_rejects_a_quantity_of_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(__('messages.errors.order_item_quantity'));

        new OrderItem(
            id: null,
            productId: 1,
            name: 'Mouse',
            sku: 'MOU-001',
            price: 50.0,
            quantity: 0,
        );
    }   

}