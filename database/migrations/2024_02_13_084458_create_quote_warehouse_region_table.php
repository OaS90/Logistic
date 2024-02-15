<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteWarehouseRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('quote_warehouse_region', function (Blueprint $table) {
            $table->bigInteger('quote_warehouse_id')->unsigned()->nullable();
            $table->bigInteger('region_id')->unsigned()->nullable();
        });

        Schema::table('quote_warehouse_region', function (Blueprint $table) {
            $table->foreign('quote_warehouse_id')->references('id')
                ->on('quote_warehouse');
            $table->foreign('region_id')->references('id')
                ->on('regions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_warehouse_region');
    }
}
