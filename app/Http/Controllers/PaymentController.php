<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Excepations\InvalidRequestException;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use App\Events\OrderPaid;

class PaymentController extends Controller
{
    // 支付宝支付
    public function payByAlipay(Order $order, Request $request) {
        // 判断订单是否属于当前用户
        $this->authorize('own', $order);

        //订单已支付或已关闭
        if ($order->paid_at || $order->close) {
            throw new InvalidRequestException('订单状态不正确');
        }

        // 调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后2位
            'subject' => '支付 Laravel Shop 的订单：'.$order->no,
        ]);
    }

    // 前端回调页面
    public function alipayReturn()
    {
        try{
            // 校验提交的参数是否合法
            app('alipay')->callback();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    // 服务器端回调
    public function alipayNotify()
    {
        // 支付完之后 支付宝给你传回来的数据 $data
        $data = app('alipay')->callback();
        // \Log 表示你可以去storage/laravel.log里查看返回来的数据
        // \Log::debug('Alipay notify', $data->all());


        /**
         * 如果订单状态不是成功或者结束，则不走后续的逻辑
         * 所有交易状态：https://docs.open.alipay.com/59/103672
         */
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统的健壮性
        if (!$order) {
            return 'fail';
        }
        // 如果这个笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝，支付宝得到这个返回后就知道我们已经处理好这笔订单了，不会再发生这笔订单的回调了。
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'   =>  Carbon::now(), // 支付时间
            'payment_method'    =>  'alipay', // 支付方式
            'payment_no'        =>  $data->trade_no //支付宝订单号
        ]);

        // 收集订单的信息
        $this->afterPaid($order);

        return app('alipay')->success();
    }

    // 微信支付
    public function payByWechat(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 校验订单状态
        if ($order->paid_at || $order->closed){
            throw new InvalidRequestException('订单状态异常');
        }

        // scan方法为拉起微信扫码支付
        $wechatOrder =  app('wechat_pay')->scan([
            'out_trade_no'  =>  $order->no,
            'total_fee'     =>  $order->total_amount * 100, // 与支付宝不同，微信支付的金额单位是分。
            'body'          => '支付 Laravel Shop 的订单：'.$order->no, // 订单描述
        ]);

        // 因为微信支付返回的是一串字符串，需要把字符串转换成二维码扫描
        $qrCode = new QrCode($wechatOrder->code_url);

        // 将生成的二维码图片数据以字符串的形式输出，并带上相应的响应类型 , 响应头：指定内容类型为图片格式
        return response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

    // 微信支付只有服务器端回调
    public function wechatNotify()
    {
        // 校验回调参数是否正确
        $data = app('wechat_pay')->callback();
        // 找到对应的订单
        $order = Order::where('no', $data->out_trade_no)->first();
        // 订单不存在则告知微信支付
        if (!$order) {
            return 'fail';
        }
        // 订单已支付
        if ($order->paid_at){
            // 告知微信支付此订单已处理
            return app('wechat_pay')->success();
        }

        // 将订单标记为已支付
        $order->update([
            'padi_at'   =>  Carbon::now(),
            'payment_method'    =>  'wechat',
            'payment_to'        =>  $data->transaction_id,
        ]);

        // 收集订单的信息
        $this->afterPaid($order);

        return app('wechat_pay')->success();
    }

    protected function afterPaid(Order $order){
        event(new OrderPaid($order));
    }

    public function wechatRefundNotify(Request $request)
    {
        // 给微信的失败响应
        $failXml = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>';
        $data = app('wechat_pay')->verify(null, true);

        // 没有找到对应的订单，原则上不可能发生，保证代码健壮性
        if(!$order = Order::where('no', $data['out_trade_no'])->first()) {
            return $failXml;
        }

        if ($data['refund_status'] === 'SUCCESS') {
            // 退款成功，将订单退款状态改成退款成功
            $order->update([
                'refund_status' =>  Order::REFUND_STATUS_SUCCESS,
            ]);
        } else {
            // 退款失败，将具体状态存入 extra 字段，并把退款状态改成失败
            $extra = $order->extra;
            $extra['refund_failed_code'] = $data['refund_status'];
            $order->update([
                'refund_status' =>  Order::REFUND_STATUS_FAILED,
                'extra' => $extra
            ]);
        }

        return app('wechat_pay')->success();
    }
}
