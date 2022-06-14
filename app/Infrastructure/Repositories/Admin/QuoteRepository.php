<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\Quote;
use Illuminate\Support\Carbon;

class QuoteRepository
{
    /**
     * @throws \Exception
     */
    public function update($quotes)
    {
        $updatedQuotes = [];

        foreach ($quotes as $quote) {
            $quoteEntry = Quote::find($quote['id']);

            if (isset($quote['tmp_quote']) && !isset($quote['tmp_date']))
                throw new \Exception('Не задан период для временной квоты');

            $quoteEntry->update([
                'quote' => $quote['quote'],
                'tmp_quote' => $quote['tmp_quote']  ?? null,
                'available_from_date' => isset($quote['tmp_date'][0]) ? Carbon::parse($quote['tmp_date'][0])->format('Y-m-d') : null,
                'available_until_date' => isset($quote['tmp_date'][1]) ? Carbon::parse($quote['tmp_date'][1])->format('Y-m-d') : null,
            ]);

            $updatedQuotes = $quoteEntry;
        }

        return $updatedQuotes;
    }

    public function getAll()
    {
        return Quote::all();
    }
}
