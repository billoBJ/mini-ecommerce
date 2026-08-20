<?php

namespace App\Http\Controllers\Api\Products;

use App\Services\Product\GetProductService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ShowProductController extends Controller
{
    public function __invoke(int $product, GetProductService $getProduct)
    {
        return new ProductResource($getProduct->handle($product));
    }
}
