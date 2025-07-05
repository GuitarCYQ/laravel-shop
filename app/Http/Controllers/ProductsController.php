<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;

class ProductsController extends Controller
{
    //
    public function index(Request $request)
    {

        // 创建一个查询构造器
        $builder = Product::query()->where('on_sale', true);
        // 判断是否有提交的 search 参数， 如果有就赋值给 $search 变量
        // search 参数用来模糊搜索商品
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // 模糊搜索商品标题、商品详情、SKU标题、SKU描述
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        // 是否有提交 order 参数，如果有就赋值给$order 变量
        // order 参数用来控制商品的排序规则
        if ($order = $request->input('order', '')) {
            // 是否是以 _asc 或者 _desc 结尾
            if(preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是以 3 个字符串之一，说明是一个合法的排序值
                if(in_array($m[1], ['price','sold_count', 'rating'])) {
                    // 根据传入的排序值来构造排序参数
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        /*
        * ->where('on_sale', true) 筛选出 on_sale 字段为 true 的数据，即上架的商品
        * ->paginate() 分页提取数据
        */
        $products = $builder->paginate(8);


        return view('products.index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'order' => $order,
            ],
        ]);
    }

    public function show(Product $product, Request $request)
    {
        // 判断商品是否已经上架，如果没有上架则抛出异常
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        // 用户未登录返回的是null， 已登录时返回的是对应的用户对象
        if($user = $request->user()) {
            // 从当前用户已收藏的商品中搜索id 为当前商品的 id 的商品
            // boolval() 函数用于把值转为布尔值
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        return view('products.show', ['product' => $product, 'favored' => $favored]);
    }


    // 收藏商品
    public function favor(Product $product, Request $request)
    {
        // $request->user() 就能获取到当前登录的用户
        $user = $request->user();
        /**
         * 通过user模型里的favoriteProducts方法（关联product的表的方法）->找这个商品id的数据
         * 通过查找如果有数据就代表用户收藏了这个产品，那就什么都不操作
         */
        if($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        // 如果没有数据，那就把该商品通过attach()方法把用户和商品进行连接
        $user->favoriteProducts()->attach($product);
        return [];
    }

    // 取消收藏
    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);
        return [];
    }

    // 收藏商品列表
    public function favorites(Request $request)
    {
        $product = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $product]);
    }
}
