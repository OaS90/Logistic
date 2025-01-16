<?php

namespace App\Infrastructure\Admin\Services;

use App\Infrastructure\Repositories\Admin\TransportCompanySettingsRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanyWarehouseRepository;
use App\Infrastructure\Admin\Services\Api\HruApi;
use App\Infrastructure\Services\Monolith\Api;

class TCSettingsService
{
    private TransportCompanyWarehouseRepository $warehouseRepo;
    private TransportCompanySettingsRepository $settingsRepo;
    private Api $monolithApi;

    private ConfigServiceApi $configServiceApi;

    public function __construct(TransportCompanyWarehouseRepository $warehouseRepo,
                                TransportCompanySettingsRepository $settingsRepo,
                                Api $monolithApi,
                                HruApi $hruApi,
                                ConfigServiceApi $configServiceApi
    )
    {
        $this->settingsRepo = $settingsRepo;
        $this->warehouseRepo = $warehouseRepo;
        $this->monolithApi = $monolithApi;
        $this->configServiceApi = $configServiceApi;
    }


    /**
     * @throws \Exception
     */
    public function save(array $warehouseSettings): bool|array
    {
        $warehousesWithSettings = [];

        foreach($warehouseSettings as $warehouse) {
            $warehouseEntity = $this->warehouseRepo->updateFieldsById($warehouse['id'], [
                'delay_days' => $warehouse['delay_days'],
                'quote' => $warehouse['quote']
            ]);

            $warehouseTCSettings = [];

            foreach($warehouse['settings'] as $setting) {
                if ($setting['enabled']) {
                    if (!in_array(true, $setting['days'])) {
                        throw new \Exception('Не выбрано ни одного дня доставки для ТК ' . $setting['tc_name']
                            . ' для склада ' . $warehouse['name']);
                    }

                    if (!$setting['last_time']) {
                        throw new \Exception('Не установлено ограничение по времени для ТК ' . $setting['tc_name']
                            . ' для склада ' . $warehouse['name']);
                    }
                }

                $tcSettings = $this->settingsRepo->updateFieldsById($setting['id'], [
                    'days' => $setting['days'],
                    'enabled' => $setting['enabled'],
                    'last_time' => $setting['last_time']
                ]);

                if ($setting['enabled']) {
                    $warehouseTCSettings[] = [
                        'tk' => $tcSettings->tc->code,
                        'weekdays' => $tcSettings->days,
                        'last_time' => $tcSettings->last_time ?? null
                    ];
                }
            }

            $warehousesWithSettings[$warehouseEntity->code] = [
                'quote' => $warehouseEntity->quote,
                'delay_days' => $warehouseEntity->delay_days,
                'shipment' => $warehouseTCSettings
            ];
        }

        if (config('app.enable_config_service_api_for_quotes')) {
            $this->configServiceApi
                ->query('api/soa/regions/transport-companies-interval-quotas-config', $warehousesWithSettings, 'PATCH');
        }

        return $this->monolithApi->sendTkQuotes($warehousesWithSettings);
    }
}
