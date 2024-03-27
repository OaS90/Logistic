<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffCategoryRegionSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tariff_region_category_settings', function (Blueprint $table) {
            $table->bigInteger('tariff_id')->unsigned();
            $table->bigInteger('region_id')->unsigned();
            $table->bigInteger('category_id')->unsigned();
            $table->boolean('is_use')->default(0);
        });

        Schema::table('tariff_region_category_settings', function (Blueprint $table) {
            $table->foreign('tariff_id')->references('id')
                ->on('tariffs');
            $table->foreign('category_id')->references('id')
                ->on('tariff_categories');
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
        Schema::dropIfExists('tariff_category_region_settings');
    }
}
