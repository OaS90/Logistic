<?php

namespace App\Infrastructure\Repositories;

use App\Domain\DTO\ApplicationDTO;
use App\Domain\Enum\ApplicationStatus;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

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
            ->where(function (Builder $query) {
                $query->where('status', 'created')
                    ->orWhereRaw('doc_ver > old_doc_ver');
            })->get();
    }

    public function create(ApplicationDTO $dto,
                           int $userId,
                           int $addressId,
                           int $warehouseId
    ): Application
    {
        return Application::updateOrCreate([
                'user_id' => $userId,
                'order_number' => $dto->orderNumber
            ],
            [
            'user_id' => $userId,
            'order_number' => $dto->orderNumber,
            'payment_type' => $dto->paymentType,
            'delivery_date' => Carbon::parse($dto->deliveryDate)->format('Y-m-d'),
            'delivery_cost' => $dto->deliveryCost ?? 0,
            'delivery_time' => $dto->deliveryTime,
            'delivery_address' => $addressId,
            'warehouse_id' => $warehouseId,
            'comment' => $dto->comment,
            'client_name' => $dto->clientFullName,
            'client_phone' => parse_phone($dto->clientPhone), // переписать в класс парсер
            'status' => ApplicationStatus::CREATED,
        ]);
    }

    public function updateStatus(string $orderId, string $status): void
    {
        $app = Application::where('order_number', $orderId)->firstOrFail();
        $app->update(['status' => $status]);
    }

    public function updateByFields(Application $app, $fields): void
    {
        $app->update($fields);
    }

    public function getAll(int $limit = 300)
    {
        return Application::with('user')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
