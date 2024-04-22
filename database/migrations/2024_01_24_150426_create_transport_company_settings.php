<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportCompanySettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_company_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_id')->comment('id транспортной компании')->unsigned();
            $table->bigInteger('tc_warehouse_id')->comment('id склада')->unsigned();
            $table->boolean('enabled')->default(0);
            $table->char('last_time', 5)->nullable();
            $table->json('days')->nullable();
            $table->timestamps();
        });

        Schema::table('transport_company_settings', function (Blueprint $table) {
            $table->foreign('tc_id')->references('id')->on('transport_companies');
            $table->foreign('tc_warehouse_id')->references('id')->on('transport_company_warehouse');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_company_settings');
    }
}
