<?php

namespace App\Infrastructure\Repositories;

use App\Domain\DTO\ProductDTO;
use App\Models\Product;
use App\Shared\Eloquent\ConvertsToUtfTrait;

class ProductRepository
{
    use ConvertsToUtfTrait;
    public function getById(int $id): Product
    {
        return Product::where('id', $id)->first();
    }

    public function updateByFields(int $id, array $fields): void
    {
        $product = $this->getById($id);
        $product->update($fields);
    }

    public function create(ProductDTO $dto, int $appId)
    {
        return Product::updateOrCreate([
            'app_id' => $appId,
            'sku' => $dto->sku,
            'name' => $dto->name
        ]
        ,[
            'app_id' => $appId,
            'name' => $this->toCp1251($dto->name),
            'brand' => $this->toCp1251($dto->brand),
            'sku' => $dto->sku,
            'count' => $dto->count,
            'cost' => $dto->cost,
            'discount_cost' => $dto->discountCost,
            'vat' => $dto->vat,
            'width' => $dto->width,
            'height' => $dto->height,
            'depth' => $dto->depth,
            'volume' => $dto->volume,
            'weight' => $dto->weight,
            'tnved' => $dto->tnved,
            'country_code' => $dto->countryCode,
            'barcode' => $dto->barcode,
            'left_to_pay' => $dto->leftToPay
        ]);
    }

    public function update(ProductDTO $dto, Product $model): bool
    {
        return $model->update([
            'name' => $dto->name,
            'brand' => $dto->brand,
            'sku' => $dto->sku,
            'count' => $dto->count,
            'cost' => $dto->cost,
            'discount_cost' => $dto->discountCost,
            'vat' => $dto->vat,
            'width' => $dto->width,
            'height' => $dto->height,
            'depth' => $dto->depth,
            'volume' => $dto->volume,
            'weight' => $dto->weight,
            'tnved' => $dto->tnved,
            'country_code' => $dto->countryCode,
            'barcode' => $dto->barcode,
            'left_to_pay' => $dto->leftToPay
        ]);
    }

    //public function getByAppIdSkuBrand(string $brand, string $sku, int $appId)
    // TODO в будущем добавить проверку по бренду, для уникальности товаров
    public function getByAppIdSkuBrand(string $sku, int $appId)
    {
        return Product::where(['sku' => $sku, 'app_id' => $appId])->first();
    }
}
