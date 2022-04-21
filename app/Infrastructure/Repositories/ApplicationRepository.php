<?php

namespace App\Infrastructure\Repositories;

use App\Models\Application;

class ApplicationRepository
{
    public function getById(int $id)
    {
        return Application::find($id);
    }

    public function getByOrderNumber(int $orderId)
    {
        return Application::where('order_number', $orderId)->first();
    }

    public function getListByUserId($userId)
    {
        return Application::where('user_id', $userId)->with('products')->get();
    }

    public function create(array $data)
    {
        // может ли быть две заявки на один заказ?
        if (!Application::where('order_number', $data['order_number'])->first())
            return Application::create($data);
    }

    public function updateStatus(int $orderId, string $status)
    {
        $app = Application::where('order_number', $orderId)->firstOrFail();
        $app->update(['status' => $status]);
    }
}
