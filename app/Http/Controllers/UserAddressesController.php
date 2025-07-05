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

    /**
     * 添加页面
     * 'address' => new UserAddress()] 传递一个空的UserAddress模型实现给视图
     * 在视图中可以通过$address变量来访问这个空模型
     */
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

    // 修改view
    public function edit(UserAddress $user_address)
    {
        /*
        * 权限校验，没有权限不能修改
        * 1.authorize('own', $user_address) 方法会获取第二个参数 $user_address 的类名App\Models\UserAddress
        * 2.执行AuthServiceProvider类中定义的Gate::guessPolicyNamesUsing自动寻找类名逻辑，找到的类是App\Polices\UserAddressPolicy
        * 3.实例化找到的UserAddressPolicy类，调用own()方法
        */
        $this->authorize('own', $user_address);
        return view('user_addresses.create_and_edit',['address' => $user_address]);
    }

    // 修改操作
    public function update(UserAddress $user_address, UserAddressRequest $request)
    {
        // 权限校验，没有权限不能修改
        $this->authorize('own', $user_address);

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

    // 删除
    public function destroy(UserAddress $user_address)
    {
        $this->authorize('own', $user_address);
        $user_address->delete();

        return [];
    }
}
