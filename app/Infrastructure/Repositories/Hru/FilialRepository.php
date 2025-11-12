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

    public function getByFilialId(string $filialId): ?Filial
    {
        return Filial::where('filial_id', $filialId)->first();
    }

    public function create(HruFilialDTO $dto): Filial
    {
        $existsEntity = Filial::where('filial_id', $dto->filialId)
            ->with(['warehouses' => function ($query) use ($dto) {
                $query->where('warehouse_id', $dto->warehouseId);
            }])
            ->first();

        if (!$existsEntity) {
            return Filial::create([
                'filial_id' => $dto->filialId,
                'name' => $dto->name,
                'default_warehouse_code' => $dto->warehouseId,
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

    public function setActiveInQuotes(int $id, bool $isActive): void
    {
        $branchOffice = $this->getById($id);
        $branchOffice->update([
            'is_active_for_quotes' => $isActive
        ]);
    }

    public function setActiveInTkQuotes(int $id, bool $isActive): void
    {
        $branchOffice = $this->getById($id);
        $branchOffice->update([
            'is_active_for_tk' => $isActive
        ]);
    }
}
