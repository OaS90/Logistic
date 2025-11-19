<?php

namespace App\Console\Commands;

use App\Domain\DTO\RegionDTO;
use App\Infrastructure\Repositories\RegionRepository;
use App\Infrastructure\Services\Kraken\Api;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncRegionsFromConfigService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-regions-from-config-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация регионов из сервиса конфигураций';

    public function __construct(private readonly Api $krakenApi, private readonly RegionRepository $regionRepo)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $result = $this->krakenApi->configServiceRequest($this->krakenApi::CONFIG_REGIONS_URI);

        if (!isset($result['regions']) || !$result) {
            Log::error('Regions sync cron error: Empty result');

            return;
        }

        foreach ($result['regions'] as $regionFromConfig) {
            $region = $this->regionRepo->getByHruId($regionFromConfig['id']);

            if ($region && !$regionFromConfig['status']) {
                $region->delete();
            }

            if (!$region && $regionFromConfig['status']) {
                $dto = new RegionDTO(hruRegionId: $regionFromConfig['id'], name: $regionFromConfig['name']);
                $this->regionRepo->create($dto);
            }
        }
    }
}
