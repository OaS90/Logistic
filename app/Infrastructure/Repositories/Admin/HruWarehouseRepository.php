<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Domain\Admin\WarehouseTcDTO;
use App\Models\Hru\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HruWarehouseRepository
{
    public function getById(int $id): Warehouse
    {
        $warehouse = Warehouse::where('id', $id)->first();

        if (!$warehouse) {
            throw new ModelNotFoundException('Не найдены настройки транспортной компании');
        }

        return $warehouse;
    }

    public function updateFieldsById(int $id, array $fields): Warehouse
    {
        $existsWarehouse = $this->getById($id);
        $existsWarehouse->update($fields);

        return $existsWarehouse;
    }

    public function getAllWithSetting(): array
    {
        return Warehouse::with(['region', 'tcSettings'])
            ->get()
            ->all();
    }

    public function getAll(): Collection
    {
        return Warehouse::all();
    }

    public function create(WarehouseTcDTO $dto)
    {
        $existsWarehouse = Warehouse::where('code', $dto->code)->first();

        if (!$existsWarehouse) {
            return Warehouse::create([
                'name' => $dto->name,
                'code' => $dto->code,
                'region_id' => $dto->regionId
            ]);
        }

        return $existsWarehouse;
    }
}
