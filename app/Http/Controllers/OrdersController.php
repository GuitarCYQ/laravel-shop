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
use App\Http\Requests\SendReviewRequest;
use App\Events\OrderReviewed;
use App\Http\Requests\ApplyRefundRequest;
use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;


class OrdersController extends Controller
{
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user  = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        $coupon = null;

        // 如果优惠提交了优惠码
        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code', $code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('优惠券不存在');
            }
        }

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'),$coupon);
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

    // 展示评价页面
    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已经支付
        if (!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 使用 load 方法加载关联数据，避免 N + 1 性能问题
        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    // 提交评价接口
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        // 检验权限
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }

        $reviews = $request->input('reviews');

        // 开启事务
        \DB::transaction(function () use ($reviews, $order) {
            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating'    =>  $review['rating'],
                    'review'    =>  $review['review'],
                    'reviewed_at'   =>  Carbon::now(),
                ]);
            }
            // 将达到标记为已评价
            $order->update(['reviewed'  =>  true]);
        });

        // 触发事件 更新商品的评分和评论数
        event(new OrderReviewed($order));

        return redirect()->back();
    }

    // 退款功能
    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 判断订单是否已付款
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可退款');
        }
        // 判断订单退款状态是否正确
        if($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');
        }
        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra                      = $order->extra ?: [];
        $extra['refund_reason']     = $request->input('reason');
        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' =>  Order::REFUND_STATUS_APPLIED,
            'extra'         =>  $extra,
        ]);

        return $order;
    }


}
