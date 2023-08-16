<?php

namespace App\Infrastructure\Repositories;

use App\Models\ApplicationObi;

class ApplicationObiRepository
{
    public function create(array $data)
    {
        $existsApp = ApplicationObi::where('order_number', $data['order_number'])
            ->where('user_id', $data['user_id'])
            ->first();

        if (!$existsApp) {
            return ApplicationObi::create($data);
        } else {
            return $existsApp;
        }
    }

    public function getListByUserId(int $userId)
    {
        return ApplicationObi::where('user_id', $userId)->get();
    }

    public function getById(int $appId)
    {
        return ApplicationObi::find($appId);
    }
}