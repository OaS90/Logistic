<?php

namespace Database\Seeders;

use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Models\Region;
use App\Models\QuoteWarehouse as Warehouse;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class NewRegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataFromFile = Excel::toArray(new ApplicationImportCsv(), storage_path('app/public/regions.csv'))[0];

        foreach ($dataFromFile as $region) {
            $newRegion = Region::create(['name' => $region[1], 'region_id' => $region[2]]);

            Warehouse::create(['region_id' => $newRegion->id, 'warehouse_name' => $region[0]]);
        }
    }
}
