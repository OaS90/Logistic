<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TariffRegionCategorySetting;

class TariffCategorySettingsRepository
{
    public function create(int $tariffId, int $regionId, int $categoryId, bool $isUse = false): ?TariffRegionCategorySetting
    {
        $exists = TariffRegionCategorySetting::where('tariff_id', $tariffId)
            ->where('region_id', $regionId)
            ->where('category_id', $categoryId)
            ->first();

        if (!$exists) {
            return TariffRegionCategorySetting::create([
                'tariff_id' => $tariffId,
                'region_id' => $regionId,
                'category_id' => $categoryId,
                'is_use' => $isUse
            ]);
        }

        return null;
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