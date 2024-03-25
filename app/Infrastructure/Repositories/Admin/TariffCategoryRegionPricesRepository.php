<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TariffCategoryPrices;

class TariffCategoryRegionPricesRepository
{
    public function deleteByTariffId(int $tariffId)
    {
        return TariffCategoryPrices::where('tariff_id', $tariffId)->delete();
    }
}