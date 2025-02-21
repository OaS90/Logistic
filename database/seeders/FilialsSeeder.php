<?php

namespace Database\Seeders;

use App\Infrastructure\Imports\ApplicationImportCsv;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class FilialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $dataFromFile = Excel::toArray(new ApplicationImportCsv(), storage_path('app/public/filials.csv'))[0];

        foreach ($dataFromFile as $filial) {

        }
    }
}
