<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteProductColumnsFromApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'product_art', 'product_brand', 'vat', 'cost', 'width', 'height',
                'depth', 'count', 'volume', 'weight', ]);
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
            $table->text('product_name')->after('order_number');
            $table->string('product_art')->after('product_name');
            $table->string('product_brand')->after('product_art');
            $table->char('vat', 20)->comment('ставка НДС в %')->after('product_brand');
            $table->float('cost')->after('vat');
            $table->float('width')->comment('ширина в см')->after('cost');
            $table->float('height')->comment('высота в см')->after('width');
            $table->float('depth')->comment('глубина в см')->after('height');
            $table->integer('count')->after('depth');
            $table->float('volume')->comment('объем в м2')->after('count');
            $table->float('weight')->comment('вес в кг')->after('volume');
        });
    }
}
