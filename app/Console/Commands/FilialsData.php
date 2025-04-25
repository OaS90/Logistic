<?php

namespace App\Console\Commands;

use App\Domain\DTO\HruFilialDTO;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Repositories\Hru\WarehouseRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Infrastructure\Services\Kraken\Api;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FilialsData extends Command
{
    public function __construct(public readonly Api $krakenApi,
                                public readonly FilialRepository $filialRepository,
                                public readonly RegionRepository $regionRepository,
                                public readonly WarehouseRepository $warehouseRepository
    )
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-filials-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получение и обновление данных по филиалам из конфиг-сервиса';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filialsData = $this->krakenApi->configServiceRequest($this->krakenApi::CONFIG_FILIALS_URI);

        if (isset($filialsData['list'])) {
            foreach ($filialsData['list'] as $item) {
                $existsFilial = $this->filialRepository->getByFilialId($item['id']);
                $region = $this->regionRepository->getByHruId($item['region_id']);

                if (!$existsFilial) {
                    $dto = new HruFilialDto(
                        filialId: $item['id'],
                        name: $item['name'],
                        warehouseId: $item['default_warehouse_id'],
                        regionId: $region->id
                    );

                    $existsFilial = $this->filialRepository->create($dto);
                } else {
                    $existsFilial->update([
                        'region_id' => $region->id,
                        'name' => $item['name'],
                    ]);
                }

                if (isset($item['warehouse_ids'])) {
                    foreach ($item['warehouse_ids'] as $warehouseCode) {
                        if ($warehouseCode == $item['default_warehouse_id']) {
                            continue;
                        }

                        $warehouse = $this->warehouseRepository->getByCode($warehouseCode);

                        if ($warehouse) {
                            if (!$existsFilial->warehouses->contains($warehouse->id)) {
                                $existsFilial->warehouses()->attach($warehouse->id);
                            }
                        } else {
                            Log::info('Attaching warehouse to filial error. ' . 'Warehouse with code ' . $warehouseCode . ' not found.');
                        }
                    }
                }
            }
        } else {
           $this->info('No results from config service');
        }

        $this->info('Done!');
    }
}
