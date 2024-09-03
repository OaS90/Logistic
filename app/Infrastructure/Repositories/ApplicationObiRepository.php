<?php

namespace App\Infrastructure\Repositories;

use App\Application\ApplicationDTOInterface;
use App\Domain\DTO\ApplicationObiDTO;
use App\Models\ApplicationObi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ApplicationObiRepository
{
    public function create(ApplicationObiDTO $dto, int $userId)
    {
        $existsApp = ApplicationObi::where('order_number', $dto->orderNumber)
            ->where('user_id', $userId)
            ->first();

        if (!$existsApp) {
            return ApplicationObi::create([
                "delivery_date" => $dto->deliveryDate,
                  "delivery_time" => $dto->deliveryTime,
                  "order_number" => $dto->orderNumber,
                  "order_type" => $dto->orderType,
                  "client_name" => $dto->clientName,
                  "phone" => $dto->phones,
                  "delivery_address" => $dto->deliveryAddress,
                  "delivery_type" => $dto->deliveryType,
                  "delivery_zone" => $dto->deliveryZone,
                  "over_delivery_zone_km" => $dto->overDeliveryZoneKm,
                  "order_weight" => $dto->orderWeight,
                  "lift_type" => $dto->liftType,
                  "lift_floor" => $dto->liftFloor,
                  "lift_weight_kg" => $dto->liftWeightKg,
                  "hand_lift_floor" => $dto->handLiftFloor,
                  "hand_lift_weight_kg" => $dto->handLiftWeightKg,
                  "transfer_distance" => $dto->transferDistance,
                  "transfer_weight" => $dto->transferWeight,
                  "products_cost" => $dto->productsCost,
                  "cost_of_transportation" => $dto->costOfTransportation,
                  "lift_cost" => $dto->liftCost,
                  "transfer_cost" => $dto->transferCost,
                  "total_delivery_cost" => $dto->totalDeliveryCost,
                  "comment" => $dto->comment,
                  'user_id' => $userId,
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