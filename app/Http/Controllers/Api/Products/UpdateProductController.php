<?php

namespace App\Http\Controllers\Api\Products;

use App\Services\Product\UpdateProductService;
use App\DTOs\Product\UpdateProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;

class UpdateProductController extends Controller
{
    public function __invoke(int $product, UpdateProductRequest $request, UpdateProductService $updateProduct)
    {
        $dto = new UpdateProductDTO(...['id' => $product, ...$request->validated()]);

        return new ProductResource($updateProduct->handle($dto));
    }
}
