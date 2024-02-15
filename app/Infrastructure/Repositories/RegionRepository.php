<?php

namespace App\Infrastructure\Repositories;

use App\Domain\DTO\Tariff\RegionDTO;
use App\Infrastructure\Services\Tariff\Factories\RegionDTOFactory;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Region;

class RegionRepository
{
    public function __construct(private RegionDTOFactory $regionDTOFactory)
    {
    }

    public function findById(int $id): ?RegionDTO
    {
        $entity = Region::where('id', $id)->first();

        if (!$entity) {
            return null;
        }

        return $this->regionDTOFactory->createFromModel($entity);
    }

    public function getAll(): Collection|array
    {
        return Region::all();
    }
}