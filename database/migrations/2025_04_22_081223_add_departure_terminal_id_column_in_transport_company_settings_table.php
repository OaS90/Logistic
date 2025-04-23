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
        Schema::table('transport_company_settings', function (Blueprint $table) {
            $table->string('departure_terminal_id', 200)->nullable()->after('delay_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_company_settings', function (Blueprint $table) {
            $table->dropColumn('departure_terminal_id');
        });
    }
};
