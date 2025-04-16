<?php

namespace Database\Seeders;

use App\Infrastructure\Repositories\Admin\QuoteRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Models\IntervalQuote;
use App\Models\Quote;
use Illuminate\Database\Seeder;

class HruFilialQuotesSeeder extends Seeder
{

    public function __construct(private readonly FilialRepository $filialRepository,
                                private readonly QuoteRepository $quoteRepository
    )
    {
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $intervals = ['10-14', '14-18', '18-22', 'inDay', 'inHour'];
        $filials = $this->filialRepository->getAll();

        foreach ($filials as $filial) {
            $quote = $this->quoteRepository->getByFields(['filial_id' => $filial->id, 'division_id' => $filial->region_id]);
            $existsWithoutFilialId = Quote::where('division_id', $filial->region_id)->first();
            $data = [
                'filial_id' => $filial->id,
                'division_id' => $filial->region_id,
                'quote' => $existsWithoutFilialId ? $existsWithoutFilialId->quote : null,
                'tmp_quote' => $existsWithoutFilialId ? $existsWithoutFilialId->temp_quote : null,
                'available_from_date' => $existsWithoutFilialId ? $existsWithoutFilialId->available_from_date : null,
                'available_until_date' => $existsWithoutFilialId ? $existsWithoutFilialId->available_until_date : null,
                'days' => $existsWithoutFilialId ? $existsWithoutFilialId->days : null,
                'time_last' => $existsWithoutFilialId ? $existsWithoutFilialId->time_last : null,
                'delivery_hours' => $existsWithoutFilialId ? $existsWithoutFilialId->delivery_hours : null,
                'blocked_date_from' => $existsWithoutFilialId ? $existsWithoutFilialId->blocked_date_from : null,
                'blocked_date_until' => $existsWithoutFilialId ? $existsWithoutFilialId->blocked_date_until : null,
                'delivery_days_from_moscow' => $existsWithoutFilialId ? $existsWithoutFilialId->delivery_days_from_moscow : null,
                'in_day_limitation' => $existsWithoutFilialId ? $existsWithoutFilialId->in_day_limitation : null
            ];

            if (!$quote) {
                $quote = Quote::create($data);
            } else {
                $quote->update($data);
            }

            foreach ($intervals as $period) {
                IntervalQuote::create(['quote_id' => $quote->id, 'period' => $period]);
            }
        }
    }
}
