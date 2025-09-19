<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TariffCategories;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public function getPricesForAllZones(TariffCategories $category,
                                int $tariffId,
                                int $regionId,
                                int $categoryId
    ): ?Collection
    {
        return $category->prices()->where('tariff_id', $tariffId)
            ->where('region_id', $regionId)
            ->where('category_id', $categoryId)
            ->get()->unique('zone_id');
    }

    public function deletePriceByZoneIdAndRegionId(TariffCategories $category,
                                                   int $tariffId,
                                                   int $regionId,
                                                   int $zoneId
    ): bool
    {
        try {
            return $category->prices()
                ->where('tariff_id', $tariffId)
                ->where('region_id', $regionId)
                ->where('zone_id', $zoneId)
                ->delete();
        } catch (\Throwable $e) {
            Log::error('delete prices error .' . $e->getMessage());
        }

        return false;
    }

    public function getByCategoryServiceIdAndProductCategoryId(int $tariffCategory): ?TariffCategories
    {
        return TariffCategories::where('result_category_id', $tariffCategory)->first();
    }

    public function getByProductCategoryId(int $id): ?TariffCategories
    {
        return TariffCategories::where('result_category_id', $id)->first();
    }

    public function deletePricesByTariffId(TariffCategories $category, int $tariffId): void
    {
        $category->prices()->where('tariff_id', $tariffId)->delete();
    }

    public function createPrice(TariffCategories $category, int $tariffId, int $regionId, int $zoneId): void
    {
        $existsPrices = $this->getPrices($category, $tariffId, $regionId, $zoneId);

        if (!$existsPrices) {
            $category->prices()->create([
                'tariff_id' => $tariffId,
                'region_id' => $regionId,
                'zone_id' => $zoneId,
                'price' => null,
                'second_price' => null
            ]);
        }
    }
}
