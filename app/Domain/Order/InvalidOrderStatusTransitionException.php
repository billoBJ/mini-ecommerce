<?php

namespace App\Domain\Order;

class InvalidOrderStatusTransitionException extends \DomainException
{
    public function __construct(
        public readonly OrderStatus $from,
        public readonly OrderStatus $to,
    ) {
        parent::__construct(__('messages.errors.invalid_order_status_transition', [
            'from' => $from->value,
            'to' => $to->value,
        ]));
    }
}
