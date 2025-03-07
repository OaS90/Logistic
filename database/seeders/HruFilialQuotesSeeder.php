<?php

namespace Database\Seeders;

use App\Infrastructure\Repositories\Admin\QuoteRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Models\IntervalQuote;
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
            $quote = $this->quoteRepository->createByFilialId($filial->id, $filial->region_id);

            foreach ($intervals as $period) {
                IntervalQuote::create(['quote_id' => $quote->id, 'period' => $period]);
            }
        }
    }
}
