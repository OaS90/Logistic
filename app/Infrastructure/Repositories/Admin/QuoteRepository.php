<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\Quote;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class QuoteRepository
{
    public function getByFields(array $fields): ?Quote
    {
        $query = Quote::query();

        foreach ($fields as $field => $value) {
            $query->where($field, $value);
        }

        return $query->first();
    }

    public function getById(int $id): ?Quote
    {
        return Quote::where('id', $id)->first();
    }

    /**
     * @throws \Exception
     */
    public function update($quotes, $userId): array
    {
        $updatedQuotes = [];

        foreach ($quotes as $quote) {
            $quoteEntity = $this->getById($quote['id']);

            if (isset($quote['tmp_quote']) && !isset($quote['tmp_date']))
                throw new \Exception('Не задан период для временной квоты');

            $quoteEntity->update([
                'quote' => (int) $quote['quote'],
                'tmp_quote' => $quote['tmp_quote']  ?? null,
                'available_from_date' => isset($quote['tmp_date'][0]) && $quote['tmp_date'][0] ? Carbon::parse($quote['tmp_date'][0])->format('Y-m-d') : null,
                'available_until_date' => isset($quote['tmp_date'][1]) && $quote['tmp_date'][1] ? Carbon::parse($quote['tmp_date'][1])->format('Y-m-d') : null,
                'days' => $quote['days'],
                'time_last' => $quote['time_last'],
                'delivery_hours' => $quote['delivery_hours'],
                'blocked_date_from' => isset($quote['blocked_dates'][0]) && $quote['blocked_dates'][0] ? Carbon::parse($quote['blocked_dates'][0])->format('Y-m-d') : null,
                'blocked_date_until' => isset($quote['blocked_dates'][1]) && $quote['blocked_dates'][1] ? Carbon::parse($quote['blocked_dates'][1])->format('Y-m-d') : null,
                'delivery_days_from_moscow' => $quote['deliveryDaysFromMoscow'],
                'in_day_limitation' => $quote['in_day_limitation'],
                'updater_id' => $userId
            ]);

            $updatedQuotes[] = $quoteEntity;
        }

        return $updatedQuotes;
    }

    public function getAll(): Collection
    {
        return Quote::all();
    }

    public function getAllWithRelations(array $relations): Collection
    {
        return Quote::with($relations)->get();
    }
}
