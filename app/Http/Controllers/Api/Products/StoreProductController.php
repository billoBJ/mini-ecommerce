<?php

namespace App\Http\Controllers\Api\Products;

use App\Services\Product\CreateProductService;
use App\DTOs\Product\CreateProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Resources\ProductResource;

class StoreProductController extends Controller
{
    public function __invoke(StoreProductRequest $request, CreateProductService $createProduct)
    {
        $dto = new CreateProductDTO(...$request->validated());

        $product = $createProduct->handle($dto);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }
}
