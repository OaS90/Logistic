<?php

namespace App\Console\Commands;

use App\Domain\DTO\WarehouseShipmentSettingsDTO;
use App\Infrastructure\Events\EventDispatcher;
use App\Models\Hru\Filial;
use App\Models\TransportCompany;
use App\Models\TransportCompanyShipmentWarehouse;
use Illuminate\Console\Command;

class GenerateStartShipmentConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-start-shipment-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует стандартный конфиг для складов отгрузки (departure_point_id Код пвз) и
        дополнительные настройки.';

    /**
     * Execute the console command.
     */
    public function handle(EventDispatcher $eventDispatcher): void
    {
        $settings = config('shipment_pickpoint');

        foreach ($settings as $tcName => $transportSettings) {
            $tc = TransportCompany::query()->where('code', $tcName)->first();

            if ($tc === null) {
                $this->info('Не найдена ТК ' . $tcName);
                continue;
            }

            foreach ($transportSettings['prod'] as $transportSetting) {
                $branchOffice = Filial::query()
                    ->where('filial_id', $transportSetting['branch_office_id'])
                    ->first();

                if ($branchOffice) {
                    $tcShipmentSettings = TransportCompanyShipmentWarehouse::query()
                        ->where('filial_id', $branchOffice->id)
                        ->where('tc_id', $tc->id)
                        ->first();


                    if (!$tcShipmentSettings) {
                        $data = [
                            'filial_id' => $branchOffice->id,
                            'tc_id' => $tc->id,
                            'departure_id' => $transportSetting['pvzcode'],
                        ];

                        $data['data'] = $this->prepareJsonData($transportSetting);
                        $tcShipmentSettings = TransportCompanyShipmentWarehouse::query()->create($data);
                    } else {
                        $data['data'] = $this->prepareJsonData($transportSetting);
                        $tcShipmentSettings->update($data);
                    }

                    $dto = new WarehouseShipmentSettingsDTO(
                        filialCode: $branchOffice->filial_code,
                        transportCompanyCode: $tc->code,
                        departurePointId: $tcShipmentSettings?->departure_id,
                        settings: $data['data']
                    );

                    $eventDispatcher->warehouseShipmentSettings($dto);
                } else {
                    $this->info('Branch office not found ' . $transportSetting['branch_office_id']);
                }
            }
        }

        $this->info('Done!');
    }

    private function prepareJsonData(array $settings): array
    {
        $preparedData = [];
        unset($settings['pvzcode']);
        unset($settings['branch_office_id']);
        foreach ($settings as $key => $value) {
            $preparedData[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        return $preparedData;
    }
}
