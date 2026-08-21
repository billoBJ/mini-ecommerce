<?php

namespace App\Http\Controllers\Api\Products;

use App\Models\Product;
use App\Services\Product\DeleteProductService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteProductController extends Controller
{
    public function __invoke(int $product, Request $request, DeleteProductService $deleteProduct)
    {
        $this->authorize('delete', Product::class);

        $deleteProduct->handle($product);

        return response()->noContent();
    }
}
