<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSetClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('teacher', 0,0)->nullable()->comment('教练');
            $table->timestamp('time')->nullable()->comment('时间');
            $table->integer('hour', 0,0)->nullable()->comment(' 时间段');
            $table->integer('course', 0,0)->nullable()->comment('课程 1正式，2体验，3文化');
            $table->integer('num', 0,0)->nullable()->comment('预约人数');
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
        Schema::dropIfExists('set_classes');
    }
}
