<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\TransportCompanyShipmentWarehouse;

class TransportCompanyShipmentWarehouseRepository
{
    public function getByTransportCompanyIdAndFilialId(int $filialId, int $tcId): ?TransportCompanyShipmentWarehouse
    {
        return TransportCompanyShipmentWarehouse::query()
            ->where('filial_id', $filialId)
            ->where('tc_id', $tcId)
            ->first();
    }
}
