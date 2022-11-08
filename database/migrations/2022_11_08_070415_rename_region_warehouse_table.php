<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRegionWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::table('region_warehouse')->truncate();
        Schema::table('region_warehouse', function (Blueprint $table) {
            $table->dropColumn('region_id');
        });
        Schema::rename('region_warehouse', 'quote_warehouse');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('quote_warehouse', 'region_warehouse');
        Schema::table('region_warehouse', function (Blueprint $table) {
            $table->integer('region_id');
        });
    }
}
