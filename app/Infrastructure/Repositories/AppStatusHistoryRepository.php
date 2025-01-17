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
    public function getByFewOrders(array $orderNumbers): array
    {
        $history = [];

        foreach ($orderNumbers as $number) {
            $historyItems = $this->getByOneOrder($number);
            $statuses = [];

            if (count($historyItems) > 0) {
                foreach ($historyItems as $item) {
                    $statuses[] = [
                        'description' => $item->getStatus($item->status) ?? 'Неизвестный статус.',
                        'status' => $item->status,
                        'datetime' => $item->date_time
                    ];
                }

            }

            $history[] = [
                'orderId' => $number,
                'statuses' => $statuses
            ];
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
