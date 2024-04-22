<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Domain\Admin\TCSettingDTO;
use App\Models\TransportCompanySettings;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransportCompanySettingsRepository
{
    public function create(TCSettingDTO $dto)
    {
        $existsSetting = TransportCompanySettings::where('tc_id', $dto->tcId)
            ->where('tc_warehouse_id', $dto->tcWarehouseId)
            ->first();

        if (!$existsSetting) {
            return TransportCompanySettings::create([
                'tc_id' => $dto->tcId,
                'tc_warehouse_id' => $dto->tcWarehouseId
            ]);
        }

        return $existsSetting;
    }

    public function getById(int $id): ?TransportCompanySettings
    {
        $settings = TransportCompanySettings::where('id', $id)->first();

        if (!$settings) {
            throw new ModelNotFoundException('Не найдены настройки транспортной компании');
        }

        return $settings;
    }

    public function updateFieldsById(int $id, array $fields): TransportCompanySettings
    {
        $existsSetting = $this->getById($id);
        $existsSetting->update($fields);

        return $existsSetting;
    }
}