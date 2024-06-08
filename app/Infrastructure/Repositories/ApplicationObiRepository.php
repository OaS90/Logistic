<?php

namespace App\Infrastructure\Repositories;

use App\Application\ApplicationDTOInterface;
use App\Models\ApplicationObi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ApplicationObiRepository
{
    public function create(ApplicationDTOInterface $dto, int $userId)
    {
        $existsApp = ApplicationObi::where('order_number', $dto->orderNumber)
            ->where('user_id', $userId)
            ->first();

        if (!$existsApp) {
            return ApplicationObi::create([
                'user_id' => $userId,
                'delivery_date' => $dto->deliveryDate,
                'delivery_time' => $dto->deliveryTime,
                'order_number' => $dto->orderNumber,
                ''
            ]);
        } else {
            return $existsApp;
        }
    }

    public function getListByUserId(int $userId)
    {
        return ApplicationObi::where('user_id', $userId)->get();
    }

    public function getListByUserIdForUpdateStatus(int $userId)
    {
        return ApplicationObi::where('user_id', $userId)
            ->where(function (Builder $query) {
                $query->where('status', 'created')
                    ->orWhereRaw('doc_ver > old_doc_ver');
            })->get();
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

    public function getAll(int $obiLimit = 100)
    {
        return ApplicationObi::with('user')
            ->orderByDesc('id')
            ->limit($obiLimit)
            ->get();
    }
}