<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\UserAddressRequest;

class UserAddressesController extends Controller
{
    //
    public function index(Request $request) {
        return view('user_addresses.index',[
            'addresses' => $request->user()->addresses,
        ]);
    }

    // 添加页面
    public function create()
    {
        return view('user_addresses.create_and_edit', ['address' => new UserAddress()]);
    }

    /**
     * 添加操作
     * Laravel会自动调用UserAddressRequest中的rules()方法来对数据进行校验
     * $request->user() 获取当前登录用户
     * user()->addresses() 获取当前用户与地址的关联关系（注意：这里不是获取当前用户的地址列表）
     * addresses->create() 在关联关系里创建一个新的记录
     * $request->only() 通过白名单的方式从用户提交的数据里获取我们需要的数据
     * return redirect()->route('user_addresses.index') 跳转回我们的地址列表页面
     */
    public function store(UserAddressRequest $request)
    {
        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        // 完成后跳转到主页
        return redirect()->route('user_addresses.index');
    }

    public function edit(UserAddress $user_address)
    {
        return view('user_addresses.create_and_edit',['address' => $user_address]);
    }

    public function update(UserAddress $user_address, UserAddressRequest $request)
    {
        //找到user_address这个用户，进行数据的修改，UserAddressRequest这个是校验类
        $user_address->update($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }
}
