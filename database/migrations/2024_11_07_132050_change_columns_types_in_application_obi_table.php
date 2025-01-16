<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsTypesInApplicationObiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications_obi', function (Blueprint $table) {
            $table->string('delivery_time', 20)->change();
            $table->string('order_number', 100)->change();
            $table->string('order_type', 150)->change();
            $table->string('client_name', 100)->change();
            $table->string('phone', 50)->change();
            $table->string('delivery_type', 100)->change();
            $table->string('delivery_zone', 100)->change();
            $table->string('lift_type')->change();
            $table->float('hand_lift_floor')->change();
            $table->float('hand_lift_weight_kg')->change();
            $table->string('status', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications_obi', function (Blueprint $table) {
            //
        });
    }
}
