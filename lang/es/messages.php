<?php

return [

    'success' => [
        'logged_in' => 'Sesión iniciada correctamente.',
        'registered' => 'Cuenta creada correctamente.',
        'logged_out' => 'Sesión cerrada correctamente.',
        'product_created' => 'Producto creado correctamente.',
        'product_updated' => 'Producto actualizado correctamente.',
        'product_deleted' => 'Producto eliminado correctamente.',
        'customer_created' => 'Cliente creado correctamente.',
        'customer_updated' => 'Cliente actualizado correctamente.',
        'customer_deleted' => 'Cliente eliminado correctamente.',
        'order_created' => 'Pedido creado correctamente.',
        'order_status_updated' => 'Estado del pedido actualizado correctamente.',
    ],

    'errors' => [
        'internal_server' => 'Error interno del servidor.',
        'unauthenticated' => 'No autenticado.',
        'unauthorized' => 'Esta acción no está autorizada.',
        'invalid_credentials' => 'Las credenciales proporcionadas son incorrectas.',
        'product_not_found' => 'Producto [:id] no encontrado.',
        'customer_not_found' => 'Cliente [:id] no encontrado.',
        'order_not_found' => 'Pedido [:id] no encontrado.',
        'model_not_found' => 'Recurso no encontrado.',
        'empty_order' => 'Un pedido debe contener al menos un artículo.',
        'insufficient_stock' => 'El producto [:id] no tiene stock suficiente: solicitado :requested, disponible :available.',
        'invalid_order_status_transition' => 'No se puede cambiar el pedido de [:from] a [:to].',
        'order_item_quantity' => 'La cantidad del artículo del pedido debe ser al menos 1.',
    ],

];
