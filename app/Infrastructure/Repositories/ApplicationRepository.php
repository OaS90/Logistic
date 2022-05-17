<?php

namespace App\Infrastructure\Repositories;

use App\Models\Application;

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

    public function getListByUserId($userId)
    {
        return Application::where('id_1c', $userId)->with('products')->get();
    }

    public function create(array $data)
    {
        return Application::create($data);
    }

    public function updateStatus(int $orderId, string $status)
    {
        $app = Application::where('order_number', $orderId)->firstOrFail();
        $app->update(['status' => $status]);
    }
}
