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
        $warehouses = $this->warehouseRepository->getAll();
        $localWarehousesIds = $warehouses->pluck('code')->map(function (string $code) {
            return (int) $code;
        })->toArray();

        $ids = $filials->pluck('id')->toArray();
        $warehouses = $this->krakenApi->configServiceRequest($this->krakenApi::CONFIG_WAREHOUSES_URI, [
            'ids' => implode(',', $ids),
        ]);

        $serviceWarehousesIds = [];

        if (isset($warehouses['success']) && $warehouses['success']) {
            foreach ($warehouses['list'] as $warehouseFromService) {
                $filial = $filials->where('id', $warehouseFromService['branch_office_id'])->first();
                $regionId = $filial->region_id;
                $code = sprintf('%05d', $warehouseFromService['id']);
                $existsWarehouse = $this->warehouseRepository->getByCode($code);
                $serviceWarehousesIds[] = $warehouseFromService['id'];

                if (!$existsWarehouse) {
                    $dto = new HruWarehouseDTO(
                        code: $code, name: $warehouseFromService['name'], regionId: $regionId
                    );

                    $this->warehouseRepository->create($dto);
                }
            }
        }

        $deletedWarehouses = array_diff($localWarehousesIds, $serviceWarehousesIds);

        foreach ($deletedWarehouses as $warehouseId) {
            $this->warehouseRepository->deleteByCode(sprintf('%05d', $warehouseId));
        }
    }
}
