<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255)->nullable()->comment('');
            $table->string('image', 255)->nullable()->comment('');
            $table->string('culture', 255)->nullable()->comment('');
            $table->string('experience', 255)->nullable()->comment('');
            $table->string('official', 255)->nullable()->comment('');
            $table->string('buy', 255)->nullable()->comment('');
            $table->string('notice', 255)->nullable()->comment('');
            $table->integer('price', 0,0)->nullable()->comment('');
            $table->integer('culture_num', 0,0)->nullable()->comment('');
            $table->integer('experience_num', 0,0)->nullable()->comment('');
            $table->integer('official_num', 0,0)->nullable()->comment('');
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
        Schema::dropIfExists('courses');
    }
}
