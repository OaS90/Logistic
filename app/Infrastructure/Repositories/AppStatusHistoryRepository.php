<?php

namespace App\Infrastructure\Repositories;

use App\Models\AppStatusHistory;
use App\Models\Application;

class AppStatusHistoryRepository
{
    /**
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        $existsStatus = AppStatusHistory::where('order_number', $data['number'])
            ->where('status', $data['status'])
            ->first();

        if (!$existsStatus) {
            AppStatusHistory::create([
                'order_number' => $data['number'],
                'status' => $data['status'],
                'date_time' => $data['dateTime']
            ]);
        }
    }

    /**
     * @param array $numbers
     * @return array
     */
    public function getByFewOrders(array $numbers): array
    {
        $history = [];

        foreach ($numbers as $number) {
            $historyItems = $this->getByOneOrder($number);

            if ($historyItems) {
                $statuses = [];

                foreach ($historyItems as $item) {
                    $statuses[] = [
                        "description" => Application::STATUSES[$item->status] ?? 'Статус не найден.',
                        "status" => $item->status,
                        "datetime" => $item->date_time
                    ];
                }

                $history[] = [
                    "orderId" => $number,
                    "statuses" => $statuses
                ];
            }
        }

        return $history;
    }

    /**
     * @param $number
     * @return mixed
     */
    public function getByOneOrder($number)
    {
        return AppStatusHistory::where('order_number', $number)->get();
    }
}
