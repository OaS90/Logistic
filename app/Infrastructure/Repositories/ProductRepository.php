<?php

namespace App\Infrastructure\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(array $data, $model)
    {
        return $model->update($data);
    }

    public function getByAppIdSkuBrand(string $brand, string $sku, int $appId)
    {
        return Product::where(['brand' => $brand, 'sku' => $sku, 'app_id' => $appId])->first();
    }
}
