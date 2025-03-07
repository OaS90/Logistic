<?php

namespace Database\Seeders;

use App\Domain\Admin\WarehouseTcDTO;
use App\Domain\DTO\HruWarehouseDTO;
use App\Infrastructure\Repositories\Hru\WarehouseRepository;
use App\Infrastructure\Repositories\RegionRepository;
use Illuminate\Database\Seeder;

/*
 * Сидер для отдельной таблицы скалов для Holodilnik.ru
 * Вместо таблицы transport_company_warehouse
 */
class HruWarehousesSeeder extends Seeder
{
    public function __construct(readonly private RegionRepository $regionRepo,
                                readonly private WarehouseRepository $warehouseRepo
    )
    {
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $warehouses = json_decode(file_get_contents(storage_path('app/public/edil_storage.json')), true);

        foreach ($warehouses as $warehouse) {
            $region = $this->regionRepo->getByHruId($warehouse['stk_regionid']);

            if (!$region) {
                echo 'Регион с id ' . $warehouse['stk_regionid'] . ' не найден';
                continue;
            }

            $warehouseDTO = new HruWarehouseDTO(
                code: $warehouse['stk_code'],
                name: $warehouse['stk_name'],
                regionId: $region->id
            );

            $this->warehouseRepo->create($warehouseDTO);
        }
    }
}
