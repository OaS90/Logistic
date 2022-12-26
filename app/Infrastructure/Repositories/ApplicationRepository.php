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
        return Application::where('user_id', $userId)->with('products')->get();
    }

    public function create(array $data)
    {
        $existsApplication = $this->getByOrderNumber($data['order_number']);

        if (!$existsApplication)
            return Application::create($data);
        else
            return $existsApplication;
    }

    public function updateStatus(string $orderId, string $status)
    {
        $app = Application::where('order_number', $orderId)->firstOrFail();
        $app->update(['status' => $status]);
    }
}
