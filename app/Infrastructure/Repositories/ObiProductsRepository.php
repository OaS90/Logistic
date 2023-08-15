<?php

namespace App\Infrastructure\Repositories;

use App\Models\ObiProduct;

class ObiProductsRepository
{
    public function create(string $info, int $appId)
    {
        $existsProduct = ObiProduct::where('name', $info)
            ->where('app_id', $appId)
            ->first();

        if (!$existsProduct) {
            ObiProduct::create([
                'app_id' => $appId,
                'name' => $info
            ]);
        } else {
            return $existsProduct;
        }
    }
}