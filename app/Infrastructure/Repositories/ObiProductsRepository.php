<?php

namespace App\Infrastructure\Repositories;

use App\Domain\DTO\ProductObiDTO;
use App\Models\ObiProduct;

class ObiProductsRepository
{
    public function create(ProductObiDTO $productDTO, int $appId)
    {
        $existsProduct = ObiProduct::where('name', $productDTO->productInfo)
            ->where('app_id', $appId)
            ->first();

        if (!$existsProduct) {
            ObiProduct::create([
                'app_id' => $appId,
                'name' => $productDTO->productInfo
            ]);
        } else {
            return $existsProduct;
        }
    }
}