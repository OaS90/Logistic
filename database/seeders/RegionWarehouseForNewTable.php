<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionWarehouseForNewTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $regions = Region::with('warehouse')->get();

        foreach ($regions as $region) {
            try {
                $region->warehouse->regions()->attach($region);
            } catch (\Throwable $e) {
                dd($e->getMessage());
            }
        }
    }
}
