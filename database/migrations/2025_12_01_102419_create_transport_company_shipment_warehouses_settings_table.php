<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transport_company_shipment_warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tc_id')->comment('Id транспортной компании');
            $table->foreign('tc_id')->references('id')->on('transport_companies')->cascadeOnDelete();
            $table->unsignedBigInteger('filial_id')->comment('Id филиала (внутренний)');
            $table->foreign('filial_id')->references('id')->on('hru_filials')->cascadeOnDelete();
            $table->string('departure_id')->comment('Код пвз')->nullable();
            $table->json('data')->nullable();
            $table->unique(['tc_id', 'departure_id']);
            $table->unique(['tc_id', 'filial_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_company_shipment_warehouses');
    }
};
