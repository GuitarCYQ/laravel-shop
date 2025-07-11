<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersAddCouponCodeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_code_id')->nullable()->after('paid_at');
            // 订单关联的优惠券被删除，自动把外键coupon_code_id 设为bull而不是删除，因为不能删除了优惠券就把对应的订单都删除了。
            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            /**
             * 删除外键关联，要遭遇 dropColumn() 删除字段调用，否则数据库会报错。
             * dropForeign()的参数可以是字符串，也可于是数组，字符串就代表删除外键名为该字符串的外键，而如果是数组则会删除该数组中字段所对应的外键。
             * coupon_code_id 字段默认的外键名 是 orders_coupon_code_id_foreign，因此需要通过数组的方式来删除
             */
            $table->dropForeign(['coupon_code_id']);
            $table->dropColumn('coupon_code_id');
        });
    }
}
