<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveForQuotesColumnInHruFilialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('hru_filials', function (Blueprint $table) {
            $table->boolean('is_active_for_quotes')->default(true)->after('region_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('hru_filials', function (Blueprint $table) {
            $table->dropColumn('is_active_for_quotes');
        });
    }
}
