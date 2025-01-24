<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeColumnsTypesFromCharToVarCharForPgsql extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE application_products
            ALTER COLUMN brand TYPE VARCHAR(30),
            ALTER COLUMN sku TYPE VARCHAR(30);');

        DB::statement('ALTER TABLE application_status_history
            ALTER COLUMN order_number TYPE VARCHAR(30),
            ALTER COLUMN status TYPE VARCHAR(20);');

        DB::statement('ALTER TABLE applications
            ALTER COLUMN client_name TYPE VARCHAR(200),
            ALTER COLUMN client_phone TYPE VARCHAR(20),
            ALTER COLUMN status TYPE VARCHAR(10);');

        DB::statement('ALTER TABLE applications_obi
            ALTER COLUMN delivery_time TYPE VARCHAR(20),
            ALTER COLUMN order_number TYPE VARCHAR(100),
            ALTER COLUMN order_type TYPE VARCHAR(150),
            ALTER COLUMN client_name TYPE VARCHAR(100),
            ALTER COLUMN phone TYPE VARCHAR(50),
            ALTER COLUMN delivery_type TYPE VARCHAR(100),
            ALTER COLUMN delivery_zone TYPE VARCHAR(100),
            ALTER COLUMN lift_type TYPE VARCHAR(20),
            ALTER COLUMN status TYPE VARCHAR(20);');

        DB::statement('ALTER TABLE delivery_addresses
            ALTER COLUMN city_name TYPE VARCHAR(150),
            ALTER COLUMN region_name TYPE VARCHAR(100),
            ALTER COLUMN city_fias TYPE VARCHAR(50),
            ALTER COLUMN street TYPE VARCHAR(100),
            ALTER COLUMN street_fias TYPE VARCHAR(50),
            ALTER COLUMN building TYPE VARCHAR(30),
            ALTER COLUMN postcode TYPE VARCHAR(6);');

        DB::statement('ALTER TABLE email_quote_send
            ALTER COLUMN email TYPE VARCHAR(50);');

        DB::statement('ALTER TABLE interval_quote
            ALTER COLUMN period TYPE VARCHAR(6);');

        DB::statement('ALTER TABLE quote_warehouse
            ALTER COLUMN warehouse_name TYPE VARCHAR(100);');

        DB::statement('ALTER TABLE regions
            ALTER COLUMN name TYPE VARCHAR(100);');

        DB::statement('ALTER TABLE transport_companies
            ALTER COLUMN code TYPE VARCHAR(50),
            ALTER COLUMN name TYPE VARCHAR(50);');

        DB::statement('ALTER TABLE transport_company_settings
            ALTER COLUMN last_time TYPE VARCHAR(5);');

        DB::statement('ALTER TABLE transport_company_warehouse
            ALTER COLUMN name TYPE VARCHAR(150),
            ALTER COLUMN code TYPE VARCHAR(20);');

        DB::statement('ALTER TABLE users
            ALTER COLUMN id_1c TYPE VARCHAR(9),
            ALTER COLUMN suffix TYPE VARCHAR(20),
            ALTER COLUMN work_phone TYPE VARCHAR(50),
            ALTER COLUMN additional_number TYPE VARCHAR(50),
            ALTER COLUMN timezone TYPE VARCHAR(100),
            ALTER COLUMN inn TYPE VARCHAR(12),
            ALTER COLUMN kpp TYPE VARCHAR(9),
            ALTER COLUMN okpo TYPE VARCHAR(10);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('char_to_var_char_for_pgsql', function (Blueprint $table) {
            //
        });
    }
}
