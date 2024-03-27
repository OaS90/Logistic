<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryServiceTariffIdInTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->integer('delivery_service_tariff_id')
                ->after('id')
                ->nullable()
                ->comment('id тарифа из сервиса доставок');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropColumn('delivery_service_tariff_id');
        });
    }
}
