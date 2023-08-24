<?php

namespace App\Infrastructure\Repositories;

use App\Models\Application;
use App\Models\ApplicationObi;
use Illuminate\Support\Facades\Log;

class ApplicationRepository
{
    public function getById(int $id)
    {
        return Application::find($id);
    }

    public function getByOrderNumber(string $orderId)
    {
        return Application::where('order_number', $orderId)->first();
    }

    public function getListByUserId(int $userId, bool $isObiPartner = false)
    {
        if ($isObiPartner) {
            return ApplicationObi::where('user_id', $userId)->with('products')->get();
        }

        return Application::where('user_id', $userId)->with('products')->get();
    }

    public function create(array $data)
    {
        $existsApplication = $this->getByOrderNumber($data['order_number']);

        if (!$existsApplication) {
            return Application::create($data);
        } else {
            $existsApplication->update($data);

            return $existsApplication;
        }
    }

    public function updateStatus(string $orderId, string $status): void
    {
        $app = Application::where('order_number', $orderId)->firstOrFail();
        $app->update(['status' => $status]);
    }

    public function updateByFields($number, $fields): void
    {
        $app = $this->getByOrderNumber($number);
        if ($app)
            $app->update($fields);
        else
            Log::info('Не удалось проставить дату заказа с сайта(Курьерской доставкой). Не найден заказ с номер ' . $number);
    }
}
