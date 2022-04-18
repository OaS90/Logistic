<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableForApplicationProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_products', function (Blueprint $table) {
            $table->id();
            $table->integer('app_id')->comment('Id заявки');
            $table->char('name', 80);
            $table->char('brand', 30);
            $table->char('sku', 30);
            $table->integer('count');
            $table->float('cost');
            $table->float('discount_cost')->nullable();
            $table->integer('vat')->comment('Ставка НДС');
            $table->float('width')->comment('ширина в см');
            $table->float('height')->comment('высота в см');
            $table->float('depth')->comment('глубина в см');
            $table->float('volume')->comment('объем в м2');
            $table->float('weight')->comment('вес в кг');
            $table->bigInteger('tnved')->comment('Код ТНВЭД')->nullable();
            $table->integer('country_code')->comment('код страны происхождения по ОКСМ')->nullable();
            $table->bigInteger('barcode')->nullable();
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
        Schema::dropIfExists('application_products');
    }
}
