<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->integer('division_id')->comment();
            $table->integer('quote')->comment('Дневная квота')->nullable();
            $table->integer('tmp_quote')->comment('Временная квота')->nullable();
            $table->date('available_from_date')->comment('Начальная дата временной квоты')->nullable();
            $table->date('available_until_date')->comment('Конечная дата временной квоты')->nullable();
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
        Schema::dropIfExists('quotes');
    }
}
