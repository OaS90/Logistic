<?php

namespace App\Infrastructure\Repositories;

use App\Models\Application;
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

    public function getListByUserId(int $userId)
    {
        return Application::where('user_id', $userId)->with('products')->get();
    }

    public function getListByUserIdForUpdateStatus(int $userId)
    {
        return Application::where('user_id', $userId)
            ->where('status', 'created')
            ->orWhere('doc_ver', '>', 'old_doc_ver')
            ->get();
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

        if ($app) {
            $app->update($fields);
        } else {
            Log::error('Не удалось найти заказ №' . $number);
        }
    }

    public function getAll(int $limit = 300)
    {
        return Application::with('user')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
