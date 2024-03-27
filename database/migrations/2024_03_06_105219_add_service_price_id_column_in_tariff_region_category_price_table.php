<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServicePriceIdColumnInTariffRegionCategoryPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tariff_region_category_prices', function (Blueprint $table) {
            $table->integer('service_price_id')
                ->after('second_price')
                ->comment('Id цены из сервиса доставок')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tariff_region_category_price', function (Blueprint $table) {
            $table->dropColumn('service_price_id');
        });
    }
}
