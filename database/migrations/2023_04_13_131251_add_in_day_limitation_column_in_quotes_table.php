<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddInDayLimitationColumnInQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            DB::statement('ALTER TABLE quotes ALTER COLUMN time_last TYPE TIME WITHOUT TIME ZONE USING time_last::time');
            $table->time('in_day_limitation')
                ->comment('ограничение для интервала день в день')
                ->after('delivery_days_from_moscow')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            DB::statement('ALTER TABLE quotes ALTER COLUMN time_last TYPE CHAR USING time_last::char');
            $table->dropColumn('in_day_limitation');
        });
    }
}
