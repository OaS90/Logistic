<?php

namespace App\Infrastructure\Repositories;

use App\Domain\DTO\Tariff\RegionDTO;
use App\Infrastructure\Services\Tariff\Factories\RegionDTOFactory;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Region;

class RegionRepository
{
    public function __construct(private readonly RegionDTOFactory $regionDTOFactory)
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
