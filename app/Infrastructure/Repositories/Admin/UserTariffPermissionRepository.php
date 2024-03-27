<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\UserTariffPermission;

class UserTariffPermissionRepository
{
    public function getByUserId(int $userId)
    {
        return UserTariffPermission::where('user_id', $userId)->get();
    }

    public function getByUserIdAndTariffId(int $userId, int $tariffId)
    {
        return UserTariffPermission::where('user_id', $userId)
            ->where('tariff_id', $tariffId)
            ->first();
    }

    public function create(int $userId, int $tariffId)
    {
        $existsRecord = UserTariffPermission::where('tariff_id', $tariffId)
            ->where('user_id', $userId)
            ->first();

        if (!$existsRecord) {
            return UserTariffPermission::create([
                'tariff_id' => $tariffId,
                'user_id' => $userId
            ]);
        }
    }

    public function deleteByTariffId(int $tariffId)
    {
        return UserTariffPermission::where('tariff_id', $tariffId)->delete();
    }
}