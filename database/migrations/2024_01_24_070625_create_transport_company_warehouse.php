<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportCompanyWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_company_warehouse', function (Blueprint $table) {
            $table->id();
            $table->char('name', 50);
            $table->char('code', 20);
            $table->integer('quote')->nullable();
            $table->bigInteger('region_id')->unsigned()->nullable();
            $table->integer('delay_days')
                ->comment('задержка дней отгрузки')
                ->default(0)
                ->nullable();
            $table->timestamps();
        });

        Schema::table('transport_company_warehouse', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_company_warehouse');
    }
}
