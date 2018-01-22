<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('name');
            $table->string('mobile')->unique();
            $table->string('password');
            $table->string('avatar');
            $table->string('sex');
            $table->string('wechat_id');
            $table->string('qq_id');
            $table->datetime('birth');
            $table->tinyInteger('status')->default(0); //0.未认证  1.已认证;
            $table->string('stuwithcard_pic');
            $table->string('id_pic');
            $table->string('stucard_pic');
            $table->bigInteger('level')->default(0);
            $table->bigInteger('exp')->default(0);
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
