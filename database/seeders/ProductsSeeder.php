<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 创建 30 个商品
        $products = \App\Models\Product::factory()->count(30)->create();
        foreach ($products as $product){
            // 创建3个SKU，并且每个SKU 的 'product_id' 字段都设为当前循环的商品id
            $skus = \App\Models\ProductSku::factory()->count(3)->create(['product_id' => $product->id]);
            // 找出价格最低的SKU价格，把商品价格设置为该价格
            $product->update(['price' => $skus->min('price')]);
        }
    }
}
