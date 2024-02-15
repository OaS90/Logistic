<?php

namespace App\Infrastructure\Admin\Services;

use App\Infrastructure\Repositories\Admin\TransportCompanySettingsRepository;
use App\Infrastructure\Repositories\Admin\TransportCompanyWarehouseRepository;
use App\Infrastructure\Admin\Services\Api\HruApi;

class TCSettingsService
{
    private TransportCompanyWarehouseRepository $warehouseRepo;
    private TransportCompanySettingsRepository $settingsRepo;
    private HruApi $hruApi;

    public function __construct(TransportCompanyWarehouseRepository $warehouseRepo,
                                TransportCompanySettingsRepository $settingsRepo,
                                HruApi $hruApi
    )
    {
        $this->settingsRepo = $settingsRepo;
        $this->warehouseRepo = $warehouseRepo;
        $this->hruApi = $hruApi;
    }


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

        return $this->hruApi->query('vtb/delivery_proxy.php?q=Holodilnik/SetQuoteTkSettings',
            $warehousesWithSettings
        );
    }
}