<?php

namespace App\Infrastructure\Repositories;

use App\Domain\DTO\RegionDTO;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Region;

class RegionRepository
{
    public function create(RegionDTO $dto): void
    {
        Region::query()->create(['region_id' => $dto->hruRegionId, 'name' => $dto->name]);
    }

    public function findById(int $id): ?Region
    {
        $entity = Region::where('id', $id)->first();

        if (!$entity) {
            return null;
        }

        return $entity;
    }

    public function getById(int $id): ?Region
    {
        return Region::where('id', $id)->first();
    }

    public function getByHruId(int $id) : ?Region
    {
        return Region::where('region_id', $id)->first();
    }

    public function findByIdWithRelationships(int $id, array $relationships)
    {
        return Region::where('id', $id)->with($relationships)->first();
    }

    public function getAll(): Collection|array
    {
        return Region::all();
    }

    public function getTariffCategoryById(Region $region, int $categoryId)
    {
        return $region->tariffCategories()
            ->where('tariff_category_region.category_id', $categoryId)
            ->first();
    }

    public function deleteTariffCategoryById(Region $region, int $categoryId): void
    {
        $region->tariffCategories()->detach($categoryId);
    }
}
