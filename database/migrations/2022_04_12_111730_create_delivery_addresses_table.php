<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->char('city_name', 40);
            $table->char('region_name', 80);
            $table->char('city_fias', 40);
            $table->char('street', 50);
            $table->char('street_fias', 50);
            $table->char('building', 30)->nullable();
            $table->integer('floor')->nullable();
            $table->integer('flat')->nullable();
            $table->integer('entrance')->nullable();
            $table->char('postcode');
            $table->boolean('use_elevator')->default(false);
        });

        DB::statement('ALTER TABLE applications ALTER COLUMN
                  delivery_address TYPE integer USING (trim(delivery_address))::integer');

        Schema::table('applications', function (Blueprint $table) {
            //$table->integer('delivery_address')->index()->change();
            $table->dropColumn([
                'flat',
                'floor',
                'entrance',
                'postcode',
                'elevator'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_addresses');
        Schema::table('applications', function (Blueprint $table) {
            $table->integer('flat');
            $table->integer('floor');
            $table->integer('entrance');
            $table->char('postcode', 100);
            $table->boolean('elevator')->default(false);
            $table->text('delivery_address')->change();
        });
    }
}
