<?php

namespace App\Infrastructure\Repositories;

use App\Models\ApplicationObi;
use Illuminate\Support\Facades\Log;

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

    public function getByOrderNumber(string $orderId)
    {
        return ApplicationObi::where('order_number', $orderId)->first();
    }

    public function updateByFields($number, $fields): void
    {
        $app = $this->getByOrderNumber($number);

        if ($app) {
            $app->update($fields);
        } else {
            Log::error('Не удалось найти заказ OBI №' . $number);
        }
    }
}