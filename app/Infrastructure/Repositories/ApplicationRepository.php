<?php

namespace App\Infrastructure\Repositories;

use App\Models\Application;

class ApplicationRepository
{
    public function getById(int $id)
    {
        return Application::find($id);
    }

    public function getListByUserId($userId)
    {
        return Application::where('user_id', $userId)->get();
    }

    public function create(array $data)
    {
        // может ли быть две заявки на один заказ?
        if (!Application::where('order_number', $data['order_number'])->first())
            return Application::create($data);
    }

    public function update()
    {

    }
}
