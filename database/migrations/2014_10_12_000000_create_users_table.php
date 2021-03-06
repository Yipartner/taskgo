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
            $table->string('mobile')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('avatar')->default("http://oz3rf0wt0.bkt.clouddn.com/18-1-22/15799237.jpg");
            $table->string('sex')->nullable();
            $table->string('wechat_id')->nullable()->unique();
            $table->string('qq_id')->nullable()->unique();
            $table->datetime('birth')->nullable();
            $table->tinyInteger('status')->default(0); //0.未认证  1.已认证;
            $table->string('stuwithcard_pic')->nullable();
            $table->string('id_pic')->nullable();
            $table->string('stucard_pic')->nullable();
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
