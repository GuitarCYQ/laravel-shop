<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    //
    public function index(Request $request)
    {
        /*
        * ->where('on_sale', true) 筛选出 on_sale 字段为 true 的数据，即上架的商品
        * ->paginate() 分页提取数据
        */
        $products = Product::query()
            ->where('on_sale', true)
            ->paginate(16);
        return view('products.index', ['products' => $products]);
    }
}
