<?php

namespace App\Console\Commands;

use App\Domain\DTO\HruWarehouseDTO;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Repositories\Hru\WarehouseRepository;
use App\Infrastructure\Services\Kraken\Api;
use Illuminate\Console\Command;
class SyncWarehousesFromConfigService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-warehouses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация складов из конфиг сервиса';

    public function __construct(public readonly Api $krakenApi,
                                public readonly WarehouseRepository $warehouseRepository,
                                public readonly FilialRepository $filialRepository
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filials = $this->filialRepository->getAll();
        $ids = $filials->pluck('filial_id')->toArray();
        $warehouses = $this->krakenApi->configServiceRequest($this->krakenApi::CONFIG_WAREHOUSES_URI, [
            'ids' => implode(',', $ids),
        ]);

        if (isset($warehouses['success']) && $warehouses['success']) {
            foreach ($warehouses['list'] as $warehouseFromService) {
                $filial = $this->filialRepository->getAll()
                    ->where('filial_id', $warehouseFromService['branch_office_id'])
                    ->first();

                $regionId = $filial->region_id;

                foreach ($warehouseFromService['shipment_warehouses'] as $warehouse) {
                    $existsWarehouse = null;
                    $code = sprintf('%05d', $warehouse['id']);
                    $existsWarehouse = $this->warehouseRepository->getByCode($code);

                    if (!$existsWarehouse) {
                        $dto = new HruWarehouseDTO(
                            code: $code,
                            name: $warehouse['name'],
                            regionId: $regionId,
                            isVirtual: $warehouse['is_virtual']
                        );

                        $warehouse = $this->warehouseRepository->create($dto);

                        if (!$warehouse->filials->contains($filial->id)) {
                            $warehouse->filials()->attach($filial->id, ['is_virtual' => $warehouse['is_virtual']]);
                        }
                    } else {
                        if ($filial) {
                            $existsWarehouse
                                ->filials()
                                ->updateExistingPivot($filial->id, ['is_virtual' => $warehouse['is_virtual']]);
                        }
                    }
                }
            }
        }
    }
}
