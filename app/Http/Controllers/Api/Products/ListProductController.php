<?php

namespace App\Http\Controllers\Api\Products;

use App\Services\Product\ListProductsService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ListProductController extends Controller
{
    public function __invoke(ListProductsService $listProducts)
    {
        return ProductResource::collection($listProducts->handle());
    }
}
