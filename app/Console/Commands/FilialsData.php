<?php

namespace App\Console\Commands;

use App\Domain\DTO\HruFilialDTO;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Infrastructure\Services\Kraken\Api;
use Illuminate\Console\Command;

class FilialsData extends Command
{
    public function __construct(public readonly Api $krakenApi,
                                public readonly FilialRepository $filialRepository,
                                public readonly RegionRepository $regionRepository
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

                    $this->filialRepository->create($dto);
                } else {
                    $existsFilial->update([
                        'region_id' => $region->id,
                        'name' => $item['name'],
                    ]);
                }
            }
        } else {
           $this->info('No results from config service');
        }

        $this->info('Done!');
    }
}
