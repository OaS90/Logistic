<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeftToPayColumnIntoApplicationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_products', function (Blueprint $table) {
            $table->float('left_to_pay')->nullable()->after('barcode')->comment('доплата');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_products', function (Blueprint $table) {
            $table->dropColumn('left_to_pay');
        });
    }
}
