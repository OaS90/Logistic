<?php

namespace App\Infrastructure\Admin\Services;

class TCWarehouseService
{
    public function prepareForVue(array $warehouses): array
    {
        $data = [];

        foreach ($warehouses as $warehouse) {
            $settings = [];
            $tcSettings = $warehouse->tcSettings;

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
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'code' => $warehouse->code,
                'quote' => $warehouse->quote ?? 0,
                'region' => $warehouse->region ?->name,
                'delay_days' => $warehouse->delay_days ?? 0,
                'show_setting' => false,
                'settings' => $settings,
                'to_save' => false
            ];
        }

        return $data;
    }
}