<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVersionsColumnsFor1cInApplicationsObiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications_obi', function (Blueprint $table) {
            $table->integer('doc_ver')->default(1)->after('status');
            $table->integer('old_doc_ver')->default(1)->after('doc_ver');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications_obi', function (Blueprint $table) {
            $table->dropColumn('doc_ver');
            $table->dropColumn('old_doc_ver');
        });
    }
}
