<?php

namespace App\Infrastructure\Admin\Services;

use App\Infrastructure\Repositories\Admin\TransportCompanySettingsRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Services\Kraken\Api as KrakenApi;
use App\Domain\DTO\Requests\FilialTCSaveRequestDTO;

class TCSettingsService
{

    public function __construct(private readonly TransportCompanySettingsRepository $settingsRepo,
                                private readonly KrakenApi $krakenApi,
                                private readonly FilialRepository $filialRepo
    )
    {}


    /**
     * @throws \Exception
     */
    public function save(array $filialsDTOsArray): bool|array
    {
        $filialsWithSettings = [];

        foreach($filialsDTOsArray as $filialDTO) {
            /* @var FilialTCSaveRequestDTO $filialDTO */
            $filialWithSettings = [];
            $filial = $this->filialRepo->getById($filialDTO->filialId);

            foreach($filialDTO->settings as $setting) {
                if ($setting->enabled) {
                    if (!in_array(true, $setting->days)) {
                        throw new \Exception('Не выбрано ни одного дня доставки для ТК ' . $setting->tcName
                            . ' для филиала ' . $filialDTO->filialCode);
                    }

                    if (!$setting->lastTime) {
                        throw new \Exception('Не установлено ограничение по времени для ТК ' . $setting->tcName
                            . ' для филиала ' . $filialDTO->filialCode);
                    }
                }

                $tcSettings = $this->settingsRepo->updateFieldsById($setting->settingId, [
                    'days' => $setting->days,
                    'enabled' => $setting->enabled,
                    'last_time' => $setting->lastTime,
                    'quote' => $filialDTO->quote,
                    'delay_days' => $filialDTO->delayDays
                ]);

                if ($setting->enabled) {
                    $filialWithSettings[] = [
                        'tk' => $tcSettings->tc->code,
                        'weekdays' => $setting->days,
                        'last_time' => $setting->lastTime ?? null
                    ];
                }
            }

            $warehouse = $filial->warehouses->first();
            $filialsWithSettings[$warehouse->code] = [
                'quote' => $filialDTO->quote,
                'delay_days' => $filialDTO->delayDays,
                'shipment' => $filialWithSettings
            ];
        }

        if (config('app.enable_config_service_api_for_quotes')) {
            $this->krakenApi
                ->configServiceRequest($this->krakenApi::CONFIG_UPDATE_TC_QUOTES_URI, $filialsWithSettings, 'PATCH');
        }

        return $this->krakenApi
            ->monolithRequest($this->krakenApi::MONOLITH_UPDATE_TC_QUOTES_URI, $filialsWithSettings, 'POST');
    }

    public function prepareForVue(): array
    {
        $data = [];
        $filials = $this->filialRepo->getAll();

        foreach ($filials as $filial) {
            $settings = [];
            $tcSettings = $filial->tcSettings;

            if (count($tcSettings) > 0) {
                foreach ($tcSettings as $setting) {
                    $settings[] = [
                        'id' => $setting->id,
                        'tc_name' => $setting->tc->name,
                        'last_time' => $setting->last_time ?? null,
                        'days' => $setting->days ?? [
                                1 => false,
                                2 => false,
                                3 => false,
                                4 => false,
                                5 => false,
                                6 => false,
                                7 => false,
                            ],
                        'enabled' => $setting->enabled
                    ];
                }
            }

            $data[] = [
                'id' => $filial->id,
                'name' => $filial->name,
                'warehouse_code' => $filial->warehouses->first() ? $filial->warehouses->first()->code : '',
                'code' => sprintf('%05d', $filial->filial_id),
                'quote' => $setting->quote ?? 0,
                'region' => $filial->region ?->name,
                'region_id' => $filial->region_id,
                'delay_days' => $setting->delay_days ?? 0,
                'show_setting' => false,
                'settings' => $settings,
                'to_save' => false
            ];
        }

        return $data;
    }
}
