<?php

namespace Database\Seeders;

use App\Domain\Admin\TCSettingDTO;
use App\Domain\Admin\WarehouseTcDTO;
use App\Infrastructure\Repositories\Admin\RegionRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanyRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanySettingsRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanyWarehouseRepository;
use Illuminate\Database\Seeder;

class WarehousesSeeder extends Seeder
{
    private TransportCompanyRepository $tcRepo;
    private TransportCompanySettingsRepository $tcSettingsRepo;
    private TransportCompanyWarehouseRepository $tcWarehouseRepo;
    private RegionRepository $regionRepo;

    public function __construct(TransportCompanyRepository $tcRepo,
                                TransportCompanySettingsRepository $tcSettingsRepo,
                                TransportCompanyWarehouseRepository $tcWarehouseRepo,
                                RegionRepository $regionRepo
    )
    {
        $this->tcRepo = $tcRepo;
        $this->tcSettingsRepo = $tcSettingsRepo;
        $this->tcWarehouseRepo = $tcWarehouseRepo;
        $this->regionRepo = $regionRepo;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $warehouses = json_decode(file_get_contents(storage_path('app/public/edil_storage.json')), true);
        $tcs = [
            'yandex' => 'Яндекс (На следующий день)',
            'boxberry' => 'Boxberry',
            'cdek' => 'CDEK',
            'pecom' => 'ПЭК',
            'rupostDirect' => 'Почта РФ'
        ];

        foreach ($tcs as $code => $name) {
            $tc = $this->tcRepo->create($code, $name);

            foreach ($warehouses as $warehouseData) {
                $region = $this->regionRepo->getByHruRegionId($warehouseData['stk_regionid']);

                if (!$region) {
                    echo 'Регион с id ' . $warehouseData['stk_regionid'] . ' не найден';
                }
                $warehouseDTO = new WarehouseTcDTO(
                    name: $warehouseData['stk_name'],
                    code: $warehouseData['stk_code'],
                    regionId: $region ?->id
                );
                $warehouse = $this->tcWarehouseRepo->create($warehouseDTO);
                $settingDTO = new TCSettingDTO(tcId: $tc->id, tcWarehouseId: $warehouse->id);
                $this->tcSettingsRepo->create($settingDTO);
            }
        }

        echo 'done';
    }
}
