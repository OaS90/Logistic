<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TariffRegionCategorySetting;

class TariffCategorySettingsRepository
{
    public function create(int $tariffId, int $regionId, int $categoryId, bool $isUse)
    {
        return TariffRegionCategorySetting::create([
            'tariff_id' => $tariffId,
            'region_id' => $regionId,
            'category_id' => $categoryId,
            'is_use' => $isUse
        ]);
    }

    public function update(int $tariffId, int $regionId, int $categoryId, array $fields)
    {
        return TariffRegionCategorySetting::where('tariff_id', $tariffId)
            ->where('region_id', $regionId)
            ->where('category_id', $categoryId)
            ->update($fields);
    }

    public function get(int $tariffId, int $regionId, int $categoryId)
    {
        return TariffRegionCategorySetting::where('tariff_id', $tariffId)
            ->where('region_id', $regionId)
            ->where('category_id', $categoryId)
            ->first();
    }

    public function delete(int $tariffId, int $regionId, int $categoryId)
    {
        return TariffRegionCategorySetting::where('tariff_id', $tariffId)
            ->where('region_id', $regionId)
            ->where('category_id', $categoryId)
            ->delete();
    }
}