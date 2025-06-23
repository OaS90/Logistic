<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TariffCategoryPrices;
use Illuminate\Support\Collection;

class TariffCategoryRegionPricesRepository
{
    public function deleteByTariffId(int $tariffId)
    {
        return TariffCategoryPrices::where('tariff_id', $tariffId)->delete();
    }

    public function getByTariffId(int $tariffId): Collection
    {
        return TariffCategoryPrices::where('tariff_id', $tariffId)->get();
    }
}
