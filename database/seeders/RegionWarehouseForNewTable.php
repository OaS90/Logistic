<?php

namespace Database\Seeders;

use App\Infrastructure\Imports\ApplicationImportCsv;
use App\Models\QuoteWarehouse;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class RegionWarehouseForNewTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $dataFromFile = Excel::toArray(new ApplicationImportCsv(), storage_path('app/public/regions_from_lk.csv'))[0];
        $warehouses = Excel::toArray(new ApplicationImportCsv(), storage_path('app/public/quote_warehouse.csv'))[0];
        unset($warehouses[0]);

        foreach ($warehouses as $warehouse) {
            $warehouseEntity = QuoteWarehouse::find($warehouse[0]);

            if (!$warehouseEntity) {
                QuoteWarehouse::create([
                    'warehouse_name' => $warehouse[1],
                ]);
            }
        }

        unset($dataFromFile[0]);
        foreach ($dataFromFile as $regionWarehouse) {
            $region = Region::where('region_id', $regionWarehouse[2])->first();
            $quoteWarehouse = QuoteWarehouse::find($regionWarehouse[3]);

            try {
                $region->warehouse()->attach($quoteWarehouse);
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }
    }
}
