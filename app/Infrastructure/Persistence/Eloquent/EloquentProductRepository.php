<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Product\Product as ProductEntity;
use App\Domain\Product\ProductRepositoryInterface;
use App\Models\Product as ProductModel;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function all(): array
    {
        return ProductModel::query()
            ->get()
            ->map(fn (ProductModel $model) => $this->toDomain($model))
            ->all();
    }

    public function find(int $id): ?ProductEntity
    {
        $model = ProductModel::query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function save(ProductEntity $product): ProductEntity
    {
        $model = $product->id
            ? ProductModel::query()->findOrFail($product->id)
            : new ProductModel();

        $model->fill([
            'name' => $product->name,
            'sku' => $product->sku,
            'description' => $product->description,
            'price' => $product->price,
            'cost' => $product->cost,
            'stock' => $product->stock,
            'active' => $product->active,
        ]);

        $model->save();

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        ProductModel::destroy($id);
    }

    private function toDomain(ProductModel $model): ProductEntity
    {
        return new ProductEntity(
            id: $model->id,
            name: $model->name,
            sku: $model->sku,
            description: $model->description,
            price: (float) $model->price,
            cost: $model->cost !== null ? (float) $model->cost : null,
            stock: $model->stock,
            active: $model->active,
        );
    }
}
