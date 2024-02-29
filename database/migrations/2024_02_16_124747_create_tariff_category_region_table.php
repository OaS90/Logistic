<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffCategoryRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tariff_category_region', function (Blueprint $table) {
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('region_id')->unsigned();
            $table->boolean('is_use')->default(0);
        });

        Schema::table('tariff_category_region', function (Blueprint $table) {
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
    public function down(): void
    {
        Schema::dropIfExists('tariff_category_region');
    }
}
