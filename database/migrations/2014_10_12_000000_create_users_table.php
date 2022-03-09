<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('patronymic')->nullable();
            $table->string('lastname')->nullable();
            $table->bigInteger('mobile_phone')->nullable();
            $table->string('position')->nullable();
            $table->char('work_phone', 100)->nullable();
            $table->char('additional_number', 50)->nullable();
            $table->char('timezone', 100)->nullable();
            $table->text('company')->nullable();
            $table->char('inn', 25)->nullable();
            $table->char('kpp', 25)->nullable();
            $table->char('okpo', 25)->nullable();
            $table->text('legal_address')->nullable();
            $table->text('avatar')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
