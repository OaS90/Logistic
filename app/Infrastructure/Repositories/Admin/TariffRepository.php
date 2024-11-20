<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\Region;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Collection;

class TariffRepository
{
    public function create(array $data)
    {
        return Tariff::create($data);
    }

    public function updateByFields(Tariff $tariff, array $fields): void
    {
        $tariff->update($fields);
    }

    public function findById(int $id): ?Tariff
    {
        return Tariff::where('id', $id)->first();
    }

    public function findByIdWithRelationships(int $id, array $relationships)
    {
        return Tariff::where('id', $id)
            ->with($relationships)
            ->first();
    }

    public function getAll(): Collection
    {
        return Tariff::get();
    }

    public function saveRegion(Tariff $tariff, Region $region, Collection $categories): ?\Illuminate\Database\Eloquent\Model
    {
        if ($tariff->regions()->where('tariff_region.region_id', $region->id)->doesntExist()) {
            return $tariff->regions()->save($region);
        }

        return null;
    }

    public function addAllRegions(Tariff $tariff, Collection $regions, Collection $categories): void
    {
        foreach ($regions as $region) {
            if ($tariff->regions()->where('tariff_region.region_id', $region->id)->doesntExist()) {
                $tariff->regions()->save($region);
            }

            foreach ($categories as $category) {
                $region->tariffCategories()->save($category);
            }

        }
    }

    public function deleteRegion(Tariff $tariff, int $regionId): int
    {
        return $tariff->regions()->detach($regionId);
    }

    public function delete(Tariff $tariff): void
    {
        $tariff->regions()->detach();
        $tariff->categorySettings()->delete();
        $tariff->categoryPrices()->delete();
        $tariff->delete();
    }

    public function getLastId(): int
    {
        return Tariff::all()->last()->id;
    }

    public function findByTariffServiceId(int $id)
    {
        return Tariff::where('delivery_service_tariff_id', $id)->first();
    }

    public function findByTariffServiceAlias(string $alias)
    {
        return Tariff::where('alias', $alias)->first();
    }
}
