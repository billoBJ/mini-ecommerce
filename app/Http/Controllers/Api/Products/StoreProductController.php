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

        return $this->withMessage(new ProductResource($product), 'messages.success.product_created')
            ->response()
            ->setStatusCode(201);
    }
}
