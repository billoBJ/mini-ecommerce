<?php

return [

    'success' => [
        'logged_in' => 'Logged in successfully.',
        'registered' => 'Account created successfully.',
        'logged_out' => 'Logged out successfully.',
        'product_created' => 'Product created successfully.',
        'product_updated' => 'Product updated successfully.',
        'product_deleted' => 'Product deleted successfully.',
        'customer_created' => 'Customer created successfully.',
        'customer_updated' => 'Customer updated successfully.',
        'customer_deleted' => 'Customer deleted successfully.',
        'order_created' => 'Order created successfully.',
        'order_status_updated' => 'Order status updated successfully.',
    ],

    'errors' => [
        'internal_server' => 'Internal server error.',
        'unauthenticated' => 'Unauthenticated.',
        'unauthorized' => 'This action is unauthorized.',
        'invalid_credentials' => 'The provided credentials are incorrect.',
        'product_not_found' => 'Product [:id] not found.',
        'customer_not_found' => 'Customer [:id] not found.',
        'order_not_found' => 'Order [:id] not found.',
        'model_not_found' => 'Resource not found.',
        'empty_order' => 'An order must contain at least one item.',
        'insufficient_stock' => 'Product [:id] has insufficient stock: requested :requested, available :available.',
        'invalid_order_status_transition' => 'Cannot transition order from [:from] to [:to].',
        'order_item_quantity' => 'Order item quantity must be at least 1.',
    ],

];
