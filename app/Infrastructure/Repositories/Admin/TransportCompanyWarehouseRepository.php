<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Domain\Admin\WarehouseTcDTO;
use App\Models\TransportCompanyWarehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransportCompanyWarehouseRepository
{
    public function getById(int $id): TransportCompanyWarehouse
    {
        $warehouse = TransportCompanyWarehouse::where('id', $id)->first();

        if (!$warehouse) {
            throw new ModelNotFoundException('Не найдены настройки транспортной компании');
        }

        return $warehouse;
    }

    public function updateFieldsById(int $id, array $fields): TransportCompanyWarehouse
    {
        $existsWarehouse = $this->getById($id);
        $existsWarehouse->update($fields);

        return $existsWarehouse;
    }

    public function getAllWithSetting(): array
    {
        return TransportCompanyWarehouse::with(['region', 'tcSettings'])
            ->get()
            ->all();
    }

    public function getAll(): Collection
    {
        return TransportCompanyWarehouse::all();
    }

    public function create(WarehouseTcDTO $dto)
    {
        $existsWarehouse = TransportCompanyWarehouse::where('code', $dto->code)->first();

        if (!$existsWarehouse) {
            return TransportCompanyWarehouse::create([
                'name' => $dto->name,
                'code' => $dto->code,
                'region_id' => $dto->regionId
            ]);
        }

        return $existsWarehouse;
    }
}