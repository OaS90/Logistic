<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;

class ChangeColumnsTypeInApplicationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('decimal')) {
            Type::addType('decimal', \Doctrine\DBAL\Types\DecimalType::class);
        }

        Schema::table('application_products', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->change();
            $table->decimal('discount_cost', 10, 2)->change();
            $table->decimal('volume', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', \Doctrine\DBAL\Types\FloatType::class);
        }

        Schema::table('application_products', function (Blueprint $table) {
            $table->double('cost', 10, 2)->change();
            $table->double('discount_cost', 10, 2)->change();
            $table->double('volume', 10, 2)->change();
        });
    }
}
