<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id', 50,"")->nullable()->comment('订单id');
            $table->integer('user_id',0,0)->nullable()->comment('用户id');
            $table->integer('course_id',0,0)->nullable()->comment('课程id');
            $table->integer('price',0,0)->nullable()->comment('价格');
            $table->integer('status',0,0)->nullable()->comment(' 0  待付款   1 已付款 2 已超时');
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
        Schema::dropIfExists('orders');
    }
}
