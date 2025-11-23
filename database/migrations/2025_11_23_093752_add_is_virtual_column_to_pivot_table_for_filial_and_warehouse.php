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
        Schema::table('hru_warehouses', function (Blueprint $table) {
            $table->dropColumn('is_virtual');
        });

        Schema::table('hru_warehouse_filial', function (Blueprint $table) {
            $table->boolean('is_virtual')->after('filial_id')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hru_warehouse_filial', function (Blueprint $table) {
            $table->dropColumn('is_virtual');
        });
    }
};
