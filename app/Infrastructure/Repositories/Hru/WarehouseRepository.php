<?php

namespace App\Infrastructure\Repositories\Hru;

use App\Domain\DTO\HruWarehouseDTO;
use App\Models\Hru\Warehouse;
use Illuminate\Database\Eloquent\Collection;

class WarehouseRepository
{
    public function create(HruWarehouseDTO $dto): Warehouse
    {
        $existsEntity = Warehouse::where('code', $dto->code)
            ->where('region_id', $dto->regionId)
            ->first();

        if (!$existsEntity) {
            return Warehouse::create([
               'code' => $dto->code,
               'name' => $dto->name,
               'region_id' => $dto->regionId,
            ]);
        }

        return $existsEntity;
    }

    public function getByCode(string $code): ?Warehouse
    {
        return Warehouse::where('code', $code)->first();
    }

    public function getAll(): Collection
    {
        return Warehouse::get();
    }

    public function deleteByCode(string $code): Warehouse
    {
        return Warehouse::where('code', $code)->delete();
    }
}
