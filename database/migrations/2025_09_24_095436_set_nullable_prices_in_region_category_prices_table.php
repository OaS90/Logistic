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
        Schema::table('tariff_region_category_prices', function (Blueprint $table) {
            $table->integer('price')->nullable()->change();
            $table->integer('second_price')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_category_prices', function (Blueprint $table) {
            $table->integer('price')->change();
            $table->integer('second_price')->change();
        });
    }
};
