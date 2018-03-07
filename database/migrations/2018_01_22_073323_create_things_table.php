<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('things', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('user_name');
            $table->string('avatar');
            $table->string('name');
            $table->string('picture_url');
            $table->string('place');
            $table->text('remarks');
            // 1:寻物启事  2：失物招领
            $table->integer('type');
            $table->integer('status')->default(0);
            // 0:发布中 2:发布者点击完成
            $table->integer('finished_by')->nullable();
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
        Schema::dropIfExists('things');
    }
}
