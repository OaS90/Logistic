<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tariff_region', function (Blueprint $table) {
            $table->bigInteger('tariff_id')->unsigned();
            $table->bigInteger('region_id')->unsigned();
        });

        Schema::table('tariff_region', function (Blueprint $table) {
            $table->foreign('tariff_id')->references('id')
                ->on('tariffs');
            $table->foreign('region_id')->references('id')
                ->on('regions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariff_region');
    }
}
