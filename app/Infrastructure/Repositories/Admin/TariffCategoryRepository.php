<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TariffCategories;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use phpseclib3\Math\BigInteger\Engines\PHP\Reductions\Barrett;

class TariffCategoryRepository
{
    public function create(array $data)
    {
        return TariffCategories::create($data);
    }

    public function getAll(): Collection
    {
        return TariffCategories::all();
    }

    public function getById(int $id): TariffCategories
    {
        return TariffCategories::where('id', $id)->first();
    }

    public function getPrices(TariffCategories $category,
                              int $tariffId,
                              int $regionId,
                              int $zoneId
    ): ?Model
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

    public function getByCategoryServiceIdAndProductCategoryId(int $tariffCategory): ?TariffCategories
    {
        return TariffCategories::where('result_category_id', $tariffCategory)->first();
    }

    public function getByProductCategoryId(int $id): ?TariffCategories
    {
        return TariffCategories::where('result_category_id', $id)->first();
    }

    public function deletePricesByTariffId(TariffCategories $category, int $tariffId)
    {
        return $category->prices()->where('tariff_id', $tariffId)->delete();
    }
}