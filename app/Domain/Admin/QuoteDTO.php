<?php

namespace App\Domain\Admin;

use Illuminate\Support\Carbon;

class QuoteDTO
{
    public function toArrayForVue($quotes): array
    {
        $data = [];

        foreach ($quotes as $quote) {
            $tmpQuoteDate = null;
            $intervals = $this->parseIntervals($quote->intervals);

            if ($quote->available_from_date && $quote->available_until_date)
                $tmpQuoteDate = [
                    str_replace('-', ', ', $quote->available_from_date),
                    str_replace('-', ', ', $quote->available_until_date)
                ];

            $quoteInfo = [
                'id' => $quote->id,
                'to_save' => false,
                'division' => $quote->region->name,
                'warehouse' => $quote->region->warehouse->warehouse_name,
                'quote' => $quote->quote,
                'tmp_quote' => $quote->tmp_quote,
                'tmp_date' => $tmpQuoteDate,
            ];

            $data[] = array_merge($quoteInfo, $intervals);
        }

        return $data;
    }

    public function parseIntervals($intervals)
    {
        $parsedIntervals = [];

        foreach ($intervals as $interval) {
            switch ($interval->period) {
                case '10-14':
                    $parsedIntervals['periodTenTwo'] = ['percent' => $interval->percent, 'active' => $interval->active];
                    break;
                case '14-18':
                    $parsedIntervals['periodTwoSix'] = ['percent' => $interval->percent, 'active' => $interval->active];
                    break;
                case '18-22':
                    $parsedIntervals['periodSixTen'] = ['percent' => $interval->percent, 'active' => $interval->active];
                    break;
                case 'inDay':
                    $parsedIntervals['inDay'] = $interval->active;
                    break;
                case 'inHour':
                    $parsedIntervals['inHour'] = $interval->active;
                    break;
            }
        }

        return $parsedIntervals;
    }

    public function makeDataForApiHru($quotes)
    {
        $data = [];

        foreach ($quotes as $quote) {
            $periods = [];
            $tmpPeriods = [];
            $dayHourPeriods = [];

            foreach ($quote->intervals as $interval) {
                if ($interval->active && $interval->percent )
                    $periods["$interval->period"] = $interval->percent;
                elseif ($interval->period == 'inDay' && $interval->active)
                    $dayHourPeriods['day'] = 1;
                elseif ($interval->period == 'inHour' && $interval->active)
                    $dayHourPeriods['hour'] = 1;
            }

            if (count($periods) > 0) {
                $mainQuote = [
                    'id' => $quote->division->region_id,
                    'limit' => $quote->quote,
                    'interval_percent' => $periods
                ];

                if ($quote->tmp_quote) {
                    $tmpPeriods = [
                        'limit_period' => $quote->tmp_quote,
                        'period' => [
                            $quote->available_from_date,
                            $quote->available_until_date
                        ]
                    ];
                }

                $data[] = array_merge($mainQuote, $tmpPeriods, $dayHourPeriods);
            }
        }

        return $data;
    }
}
