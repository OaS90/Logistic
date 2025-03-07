<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Domain\Admin\TCSettingDTO;
use App\Models\TransportCompanySettings;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class TransportCompanySettingsRepository
{
    public function getAll(): Collection
    {
        return TransportCompanySettings::get();
    }

    public function create(TCSettingDTO $dto)
    {
        $existsSetting = TransportCompanySettings::where('tc_id', $dto->tcId)
            ->where('tc_warehouse_id', $dto->warehouseId)
            ->where('filial_id', $dto->filialId)
            ->first();

        if (!$existsSetting) {
            return TransportCompanySettings::create([
                'tc_id' => $dto->tcId,
                'tc_warehouse_id' => $dto->warehouseId,
                'filial_id' => $dto->filialId
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
