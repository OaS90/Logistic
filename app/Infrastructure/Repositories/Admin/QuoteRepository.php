<?php

namespace App\Infrastructure\Repositories\Admin;

use App\Models\Quote;
use Illuminate\Support\Carbon;

class QuoteRepository
{
    public function update($quotes)
    {
        $updatedQuotes = [];

        foreach ($quotes as $quote) {
            $quoteEntry = Quote::find($quote['id']);
            $availableFromDate = $quote['tmp_date'][0];
            $availableUntilDate = $quote['tmp_date'][1];
            $quoteEntry->update([
                'quote' => $quote['quote'],
                'tmp_quote' => $quote['tmp_quote'],
                'available_from_date' => Carbon::parse($availableFromDate)->format('Y-m-d'),
                'available_until_date' => Carbon::parse($availableUntilDate)->format('Y-m-d'),
            ]);

            $updatedQuotes = $quoteEntry;
        }

        return $updatedQuotes;
    }
}
