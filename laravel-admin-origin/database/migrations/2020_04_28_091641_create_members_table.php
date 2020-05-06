<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable()->comment('姓名');
            $table->string('head_icon', 255)->nullable()->comment('头像');
            $table->string('mobile', 11)->nullable()->comment('手机号');
            $table->integer('sex', 0,0)->nullable()->comment('性别 1男 2女');
            $table->integer('culture_num', 0,0)->nullable()->comment('文化课时');
            $table->integer('experience_num', 0,0)->nullable()->comment('体验课时');
            $table->integer('official_num', 0,0)->nullable()->comment('正式课时');
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
        Schema::dropIfExists('members');
    }
}
