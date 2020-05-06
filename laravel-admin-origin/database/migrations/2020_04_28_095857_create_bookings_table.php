<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable()->comment('姓名');
            $table->string('mobile', 11)->nullable()->comment('手机号');
            $table->timestamp('time')->nullable()->comment('时间');
            $table->integer('teacher', 0,0)->nullable()->comment('教练');
            $table->integer('hour', 0,0)->nullable()->comment(' 时间段');
            $table->integer('course', 0,0)->nullable()->comment('课程 1正式，2体验，3文化');
            $table->string('comment', 255)->nullable()->comment('备注');
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
        Schema::dropIfExists('bookings');
    }
}
