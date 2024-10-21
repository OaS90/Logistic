<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInDayLimitationColumnInQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE quotes ALTER COLUMN
                  time_last TYPE time(0) USING (trim(time_last))::time(0)');

        Schema::table('quotes', function (Blueprint $table) {
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
            $table->char('time_last', 5)->change();
            $table->dropColumn('in_day_limitation');
        });
    }
}
