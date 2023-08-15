<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsObiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications_obi', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->date('delivery_date');
            $table->char('delivery_time', 20);
            $table->char('order_number', 100);
            $table->char('order_type', 150);
            $table->char('client_name', 100);
            $table->char('phone', 50)->nullable();
            $table->text('delivery_address')->nullable();
            $table->char('delivery_type', 100);
            $table->char('delivery_zone', 100);
            $table->float('over_delivery_zone_km')->nullable();
            $table->float('order_weight');
            $table->char('lift_type')->nullable()->comment('Вид подъёма');
            $table->integer('lift_floor')->nullable()->comment('Этаж подъёма на лифте');
            $table->float('lift_weight_kg')->nullable();
            $table->integer('hand_lift_floor')->nullable();
            $table->integer('hand_lift_weight_kg')->nullable();
            $table->float('transfer_distance')->nullable();
            $table->float('transfer_weight')->nullable();
            $table->float('products_cost')->nullable();
            $table->float('cost_of_transportation')->nullable();
            $table->float('lift_cost')->nullable();
            $table->float('transfer_cost')->nullable();
            $table->float('total_delivery_cost')->nullable();
            $table->text('comment')->nullable();
            $table->char('status', 20)->default('created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications_obi');
    }
}
