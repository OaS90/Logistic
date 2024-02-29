<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TariffCategories;
use Illuminate\Database\Eloquent\Collection;

class TariffCategoryRepository
{
    public function getAll(): Collection
    {
        return TariffCategories::all();
    }

    public function getById(int $id): TariffCategories
    {
        return TariffCategories::where('id', $id)->first();
    }

    public function getPrices(TariffCategories $category, int $tariffId, int $regionId, int $zoneId)
    {
        return $category->prices()
            ->where('tariff_id', $tariffId)
            ->where('region_id', $regionId)
            ->where('zone_id', $zoneId)
            ->first();
    }

    public function deletePriceByZoneIdAndRegionId(TariffCategories $category,
                                                   int $tariffId,
                                                   int $regionId,
                                                   int $zoneId
    ): bool
    {
        return $category->prices()
            ->where('tariff_id', $tariffId)
            ->where('region_id', $regionId)
            ->where('zone_id', $zoneId)
            ->delete();
    }
}