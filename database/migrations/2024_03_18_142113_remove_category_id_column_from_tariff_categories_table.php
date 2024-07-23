<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCategoryIdColumnFromTariffCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('tariff_categories', function (Blueprint $table) {
            $table->dropColumn('category_id_test');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('tariff_categories', function (Blueprint $table) {
            $table->integer('category_id')->nullable();
        });
    }
}
