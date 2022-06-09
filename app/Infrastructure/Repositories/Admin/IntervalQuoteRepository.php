<?php

namespace App\Infrastructure\Repositories\Admin;
use App\Models\IntervalQuote;
use Illuminate\Support\Facades\Log;

class IntervalQuoteRepository
{
    public function update(array $data)
    {
        $intervals = ['10-14', '14-18', '18-22', 'inDay', 'inHour'];

        foreach ($data as $quote) {
            foreach ($intervals as $period) {
                switch ($period) {
                    case '10-14':
                        IntervalQuote::where([['quote_id', $quote['id']], ['period', $period]])
                            ->update(['percent' => $quote['periodTenTwo']['percent'], 'active' => $quote['periodTenTwo']['active']]);
                        break;
                    case '14-18':
                        IntervalQuote::where([['quote_id', $quote['id']], ['period', $period]])
                            ->update(['percent' => $quote['periodTwoSix']['percent'], 'active' => $quote['periodTwoSix']['active']]);
                        break;
                    case '18-22':
                        IntervalQuote::where([['quote_id', $quote['id']], ['period', $period]])
                            ->update(['percent' => $quote['periodSixTen']['percent'], 'active' => $quote['periodSixTen']['active']]);
                        break;
                    case 'inDay':
                        IntervalQuote::where([['quote_id', $quote['id']], ['period', $period]])
                            ->update(['active' => $quote['inDay']]);
                        break;
                    case 'inHour':
                        IntervalQuote::where([['quote_id', $quote['id']], ['period', $period]])
                            ->update(['active' => $quote['inHour']]);
                        break;
                }
            }
        }
    }
}
