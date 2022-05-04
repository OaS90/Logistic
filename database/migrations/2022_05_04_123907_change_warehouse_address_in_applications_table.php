<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWarehouseAddressInApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('warehouse_address');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->integer('warehouse_id')->after('delivery_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('warehouse_id');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->text('warehouse_address');
        });
    }
}
