<?php

namespace App\Infrastructure\Repositories\Hru;

use App\Domain\DTO\HruFilialDTO;
use App\Models\Hru\Filial;
use Illuminate\Support\Collection;

class FilialRepository
{
    public function getById(int $id): ?Filial
    {
        return Filial::where('id', $id)->first();
    }

    public function create(HruFilialDTO $dto): Filial
    {
        $existsEntity = Filial::where('code', $dto->code)
            ->with(['warehouses' => function ($query) use ($dto) {
                $query->where('warehouse_id', $dto->warehouseId);
            }])
            ->first();

        if (!$existsEntity) {
            return Filial::create([
                'code' => $dto->code,
                'name' => $dto->name,
                'region_id' => $dto->regionId
            ]);
        }

        return $existsEntity;
    }

    public function getAll(): Collection
    {
        return Filial::all();
    }

    public function getByWarehouseCode(string $code)
    {
        return Filial::whereHas('warehouses', function ($query) use ($code) {
            $query->where('code', $code);
        })->get();
    }
}
