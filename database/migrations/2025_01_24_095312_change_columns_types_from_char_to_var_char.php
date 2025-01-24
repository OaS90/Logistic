<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsTypesFromCharToVarChar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_products', function (Blueprint $table) {
            $table->string('brand', 30)->change();
            $table->string('sku', 30)->change();
        });

        Schema::table('application_status_history', function (Blueprint $table) {
            $table->string('order_number', 30)->change();
            $table->string('status', 20)->change();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->string('client_name', 200)->change();
            $table->string('client_phone', 20)->change();
            $table->string('status', 10)->change();
        });

        Schema::table('applications_obi', function (Blueprint $table) {
            $table->string('delivery_time', 20)->change();
            $table->string('order_number', 100)->change();
            $table->string('order_type', 150)->change();
            $table->string('client_name', 100)->change();
            $table->string('phone', 50)->change();
            $table->string('delivery_type', 100)->change();
            $table->string('delivery_zone', 100)->change();
            $table->string('lift_type', 20)->change();
            $table->string('status', 20)->change();
        });

        Schema::table('delivery_addresses', function (Blueprint $table) {
            $table->string('city_name', 150)->change();
            $table->string('region_name', 100)->change();
            $table->string('city_fias', 50)->change();
            $table->string('street', 100)->change();
            $table->string('street_fias', 50)->change();
            $table->string('building', 30)->change();
            $table->string('postcode', 6)->change();
        });

        Schema::table('email_quote_send', function (Blueprint $table) {
            $table->string('email', 50)->change();
        });

        Schema::table('interval_quote', function (Blueprint $table) {
            $table->string('period', 6)->change();
        });

        Schema::table('quote_warehouse', function (Blueprint $table) {
            $table->string('warehouse_name', 100)->change();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->string('name', 100)->change();
        });

        Schema::table('transport_companies', function (Blueprint $table) {
            $table->string('code', 50)->change();
            $table->string('name', 50)->change();
        });

        Schema::table('transport_company_settings', function (Blueprint $table) {
            $table->string('last_time', 5)->change();
        });

        Schema::table('transport_company_warehouse', function (Blueprint $table) {
            $table->string('name', 150)->change();
            $table->string('code', 20)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('id_1c', 9)->change();
            $table->string('suffix', 20)->change();
            $table->string('work_phone', 50)->change();
            $table->string('additional_number', 50)->change();
            $table->string('timezone', 100)->change();
            $table->string('inn', 12)->change();
            $table->string('kpp', 9)->change();
            $table->string('okpo', 10)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('char_to_var_char', function (Blueprint $table) {
            //
        });
    }
}
