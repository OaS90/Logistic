<?php

namespace Database\Seeders;

use App\Infrastructure\Imports\ApplicationImport;
use App\Models\Division;
use App\Models\IntervalQuote;
use App\Models\Quote;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class QuotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataFromFile = Excel::toArray(new ApplicationImport(), storage_path('app/public/divisions.csv'))[0];
        $intervals = ['10-14', '14-18', '18-22', 'inDay', 'inHour'];

        foreach ($dataFromFile as $divisionName) {
            $division = Division::create(['name' => $divisionName[0]]);

            $quote = Quote::create(['division_id' => $division->id]);

            foreach ($intervals as $period) {
                IntervalQuote::create(['quote_id' => $quote->id, 'period' => $period]);
            }

        }
    }
}
