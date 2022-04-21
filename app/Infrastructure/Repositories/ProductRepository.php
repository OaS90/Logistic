<?php

namespace App\Infrastructure\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function create(array $data)
    {
        return Product::create($data);
    }
}
