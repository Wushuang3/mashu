<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id', 0,0)->nullable()->comment('');
            $table->integer('teacher_id', 0,0)->nullable()->comment('');
            $table->integer('course_id', 0,0)->nullable()->comment('');
            $table->integer('score', 0,0)->nullable()->comment('');
            $table->integer('is_show', 0,0)->nullable()->comment('');
            $table->string('content', 255)->nullable()->comment('');
            $table->string('tags', 255)->nullable()->comment('');
            $table->string('imgs', 255)->nullable()->comment('');

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
        Schema::dropIfExists('ratings');
    }
}
