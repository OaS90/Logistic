<?php

namespace App\Infrastructure\Repositories;

use App\Models\ObiProduct;

class ProductObiRepository
{
    public function getByAppId(int $appId)
    {
        return ObiProduct::where('app_id', $appId)->first();
    }
}