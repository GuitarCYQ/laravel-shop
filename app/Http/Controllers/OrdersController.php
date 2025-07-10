<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Illuminate\Http\Request;
use App\Services\OrderService;


class OrdersController extends Controller
{
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user  = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
    }

    // 订单列表
    public function index(Request $request) {
        $order = Order::query()
            //使用with方法预加载，避免 N+1 问题
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $order]);
    }

    // 订单详情
    public function show(Order $order, Request $request) {
        // 调用策略，只允许订单的创建者才能查看订单
        $this->authorize('own',$order);

        // laod() 跟 with() 差不多，都是延迟预加载，都说load是在已经查询出来的模型上调用，而with是在ORM上调用
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    // 确认收货
    public function received(Order $order, Request $request)
    {
        // 校验权限，只有自己的订单才可以查看
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        // 返回订单信息
        return $order;
    }
}
