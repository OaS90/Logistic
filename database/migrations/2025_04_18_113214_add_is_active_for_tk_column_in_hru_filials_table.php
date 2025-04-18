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
        Schema::table('hru_filials', function (Blueprint $table) {
            $table->boolean('is_active_for_tk')->default(true)->after('is_active_for_quotes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hru_filials', function (Blueprint $table) {
            $table->dropColumn('is_active_for_tk');
        });
    }
};
