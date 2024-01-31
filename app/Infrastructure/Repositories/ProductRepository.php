<?php

namespace App\Infrastructure\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getById(int $id): Product
    {
        return Product::where('id', $id)->first();
    }

    public function updateByFields(int $id, array $fields): void
    {
        $product = $this->getById($id);
        $product->update($fields);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(array $data, $model)
    {
        return $model->update($data);
    }

    //public function getByAppIdSkuBrand(string $brand, string $sku, int $appId)
    // TODO в будущем добавить проверку по бренду, для уникальности товаров
    public function getByAppIdSkuBrand(string $sku, int $appId)
    {
        return Product::where(['sku' => $sku, 'app_id' => $appId])->first();
    }
}
