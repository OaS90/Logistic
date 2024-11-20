<?php

namespace Database\Seeders;

use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Models\QuoteWarehouse as Warehouse;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class NewRegionWarehouseQuoteSeeder extends Seeder
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
            $newWarehouse = Warehouse::where('warehouse_name', $region[0])->first();

            if (!$newWarehouse)
                $newWarehouse = Warehouse::create(['warehouse_name' => $region[0]]);

            Region::create(['name' => $region[1], 'region_id' => $region[2]]);
        }
    }
}
