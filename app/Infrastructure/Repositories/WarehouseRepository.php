<?php

namespace App\Infrastructure\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function create(array $data)
    {
         return Warehouse::create([
             'store_id' => $data['storeId'],
             'address' => $data['storeAddress'],
             'user_id' => $data['userId']
         ]);
    }

    public function findByAddressOrStoreId($address, $storeId = null)
    {
        return Warehouse::where('address', 'LIKE', '%' . $address . '%')
            ->where('store_id', $storeId)->first();
    }
}
