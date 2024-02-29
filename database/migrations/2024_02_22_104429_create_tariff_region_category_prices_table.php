<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffRegionCategoryPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tariff_region_category_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tariff_id');
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('zone_id');
            $table->integer('price')->default(0);
            $table->integer('second_price')->default(0);
            $table->timestamps();
        });

        Schema::table('tariff_region_category_prices', function (Blueprint $table) {
            $table->foreign('tariff_id')->references('id')
                ->on('tariffs');
            $table->foreign('region_id')->references('id')
                ->on('regions');
            $table->foreign('category_id')->references('id')
                ->on('tariff_categories');
            $table->foreign('zone_id')->references('id')
                ->on('tariff_region_zones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tariff_region_category_prices');
    }
}
