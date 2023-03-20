<?php

namespace App\Domain\Admin;

use Illuminate\Support\Carbon;

class QuoteDTO
{
    public function toArrayForVue($quotes): array
    {
        $data = [];

        foreach ($quotes as $quote) {
            $tmpQuoteDate = [];
            $blockedDates = [];
            $intervals = $this->parseIntervals($quote->intervals);

            if ($quote->available_from_date && $quote->available_until_date)
                $tmpQuoteDate = [
                    str_replace('-', ', ', $quote->available_from_date),
                    str_replace('-', ', ', $quote->available_until_date)
                ];

            if ($quote->blocked_date_from && $quote->blocked_date_until)
                $blockedDates = [
                    str_replace('-', ', ', $quote->blocked_date_from),
                    str_replace('-', ', ', $quote->blocked_date_until)
                ];

            $quoteInfo = [
                'id' => $quote->id,
                'to_save' => false,
                'division' => $quote->region->name,
                'warehouse' => $quote->region->warehouse->warehouse_name,
                'quote' => $quote->quote,
                'tmp_quote' => $quote->tmp_quote,
                'tmp_date' => $tmpQuoteDate,
                'days' => $quote->days ?? [
                    1 => null, //пн
                    2 => null, //вт
                    3 => null, //ср
                    4 => null, //чт
                    5 => null, //пт
                    6 => null, //сб
                    7 => null //вс
                ],
                'time_last' => $quote->time_last,
                'delivery_hours' => $quote->delivery_hours ?? ['from' => null, 'till' => null],
                'blocked_dates' => $blockedDates,
                'deliveryDaysFromMoscow' => $quote->delivery_days_from_moscow
            ];

            $data[] = array_merge($quoteInfo, $intervals);
        }

        return $data;
    }

    public function parseIntervals($intervals): array
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

    public function makeDataForApiHru($quotes): array
    {
        $data = [];

        foreach ($quotes as $quote) {
            $periods = [];
            $tmpPeriods = [];
            $dayHourPeriods = [];
            $days = [];
            $blockedDates = [];

            foreach ($quote->intervals as $interval) {
                if ($interval->active && $interval->percent )
                    $periods["$interval->period"] = $interval->percent;
                elseif ($interval->period == 'inDay' && $interval->active)
                    $dayHourPeriods['day'] = 1;
                elseif ($interval->period == 'inHour' && $interval->active)
                    $dayHourPeriods['hour'] = 1;
            }

            if ($quote->days > 0) {
                foreach ($quote->days as $day => $isActive) {
                    if ($isActive)
                        $days[] = $day;
                }
            }

            if ($quote->quote) {
                $mainQuote = [
                    'id' => $quote->region->region_id,
                    'limit' => $quote->quote,
                ];

                if (count($periods) > 0)
                    $mainQuote['interval_percent'] = $periods;

                if ($days)
                    $mainQuote['days'] = $days;

                if ($quote->time_last)
                    $mainQuote['time_last'] = $quote->time_last;

                if ($quote->tmp_quote) {
                    $tmpPeriods = [
                        'limit_period' => $quote->tmp_quote,
                        'period' => [
                            $quote->available_from_date,
                            $quote->available_until_date
                        ]
                    ];
                }

                if ($quote->blocked_date_from && $quote->blocked_date_until) {
                    $blockedDates = [
                        'blocked_dates' => [
                            $quote->blocked_date_from,
                            $quote->blocked_date_until
                        ]
                    ];
                }

                if ($quote->delivery_hours) {
                    $mainQuote['delivery_hours'] = $quote->delivery_hours;
                }

                if ($quote->delivery_days_from_moscow) {
                    $mainQuote['deliveryDaysFromMoscow'] = $quote->delivery_days_from_moscow;
                }

                $data[] = array_merge($mainQuote, $tmpPeriods, $dayHourPeriods, $blockedDates);
            }
        }

        return $data;
    }
}
