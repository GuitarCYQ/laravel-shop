<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // // 往服务容器中注入一个名为 alipay 的单例对象，第一次从容器中取对象时会调用回调函数生成对应的对象并保存到容器中，之后再取的收获直接从容器中的对象返回
        // $this->app->singleton('alipay', function() {
        //     $config = config('pay.alipay');
        //     // 判断当前项目运行环境是否为线上
        //     if(app()->environment() !== 'production') {
        //         $config['mode']         = 'dev';
        //         $config['log']['level'] = Logger::DEBUG;
        //     } else {
        //         $config['log']['level'] = Logger::WARNING;
        //     }
        //     // 调用 Yansongda\pay 来创建一个支付宝支付对象
        //     return Pay::alipay($config);
        // });

        // $this->app->singleton('wechat_pay', function() {
        //     $config = config('pay.wechat');
        //     // 判断当前项目运行环境是否为线上
        //     if (app()->environment() !== 'production') {
        //         $config['log']['level'] = Logger::DEBUG;
        //     } else {
        //         $config['log']['level'] = Logger::WARNING;
        //     }
        //     // 调用 Yansongda\pay 来创建一个微信支付对象
        //     return Pay::wechat($config);
        // });

        $config = config('pay');
        //判断当前项目运行环境是否为线上环境
        if (app()->environment() !== 'production') {
            $config['alipay']['default']['mode'] = $config['wechat']['default']['mode'] = Pay::MODE_SANDBOX;
            $config['logger']['level'] = 'debug';
        } else {
            $config['logger']['level'] = 'info';
        }

        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay', function() use ($config) {
            //调用Yansongda\Pay来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function() use ($config) {
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
