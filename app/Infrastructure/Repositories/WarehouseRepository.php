<?php

namespace App\Infrastructure\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function create(array $data)
    {
         return Warehouse::create($data);
    }

    public function findByAddressOrStoreId($address, $storeId = null)
    {
        return Warehouse::where('address', 'LIKE', '%' . $address . '%')
            ->where('store_id', $storeId)->first();
    }
}
