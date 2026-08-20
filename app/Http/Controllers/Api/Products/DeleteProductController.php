<?php

namespace App\Http\Controllers\Api\Products;

use App\Services\Product\DeleteProductService;
use App\Http\Controllers\Controller;

class DeleteProductController extends Controller
{
    public function __invoke(int $product, DeleteProductService $deleteProduct)
    {
        $deleteProduct->handle($product);

        return response()->noContent();
    }
}
