<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilialIdColumnInTransportCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('transport_company_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('filial_id')->nullable()->after('tc_warehouse_id');
            $table->foreign('filial_id')->references('id')->on('hru_filials')->onDelete('cascade');
            $table->integer('quote')->nullable()->after('filial_id');
            $table->integer('delay_days')
                ->after('quote')
                ->comment('задержка дней отгрузки')
                ->default(0)
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('transport_company_settings', function (Blueprint $table) {
            $table->dropColumn('filial_id');
        });
    }
}
