<?php

namespace App\Infrastructure\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function create(array $data)
    {
         return Warehouse::create($data);
    }

    public function findByStoreId($storeId)
    {
        return Warehouse::where('store_id', $storeId)->first();
    }

    public function findByAddressAndUserId(int $userId, string $address)
    {
        return Warehouse::where('user_id', $userId)
            ->where('address', 'LIKE', '%' . $address . '%')
            ->first();
    }
}
