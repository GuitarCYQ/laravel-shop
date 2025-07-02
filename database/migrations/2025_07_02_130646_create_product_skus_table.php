<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('description');
            $table->decimal('price', 10, 2);
            // 创建 无符号 整数类型 的外键字段
            $table->unsignedInteger('stock');
            $table->unsignedBigInteger('product_id');
            // product_id 关联 produces 表的 id 只要produces里的记录被删除了，就回调用onDelete()一起删除produce_sku表里的数据
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('product_skus');
    }
}
