<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('order_number');
            $table->text('product_name');
            $table->string('product_art');
            $table->string('product_brand');
            $table->string('payment_type');
            $table->char('vat', 20)->comment('ставка НДС в %');
            $table->char('cost', 100);
            $table->float('width')->comment('ширина в см');
            $table->float('height')->comment('высота в см');
            $table->float('depth')->comment('глубина в см');
            $table->integer('count');
            $table->float('volume')->comment('объем в м2');
            $table->float('weight')->comment('вес в кг');
            $table->text('warehouse_address')->comment('адрес склада отгрузки');
            $table->date('delivery_date');
            $table->string('delivery_time');
            $table->integer('delivery_address');
            $table->integer('flat');
            $table->integer('floor');
            $table->integer('entrance');
            $table->char('postcode', 100);
            $table->boolean('elevator')->default(false);
            $table->text('comment')->nullable();
            $table->char('client_name', 200)->comment('ФИО покупателя');
            $table->char('client_phone', 20);
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
        Schema::dropIfExists('applications');
    }
}
