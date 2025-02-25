<?php

namespace App\Infrastructure\Repositories\Hru;

use App\Domain\DTO\HruFilialDTO;
use App\Models\Hru\Filial;

class FilialRepository
{
    public function create(HruFilialDTO $dto)
    {
        $existsEntity = Filial::where('code', $dto->code)
            ->where('warehouse_id', $dto->warehouseId)
            ->first();

        if (!$existsEntity) {
            return Filial::create([
                'code' => $dto->code,
                'name' => $dto->name,
                'warehouse_id' => $dto->warehouseId
            ]);
        }

        return $existsEntity;
    }
}
