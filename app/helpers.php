<?php


// 将当前请求的路由名称转换为css类名称，作用是运行对某个页面做页面样式定制
function route_class(){
    return  str_replace('.','-',Route::currentRouteName());
}


/**
 * 本地开发环境时，支付回调就会被发往 Ngrok 的域名然后映射到站点
 * 而正式环境的时候 ngrok_url() 函数的作用与 route() 函数一致 不会影响到其他
 */

function ngrok_url($routeName, $parameters = [])
{
    // 开发环境，并且配置了 NGROK_URL
    if(app()->environment('local') && $url = config('app.ngrok_url')) {
        // route() 函数第三个参数代表身份绝对路径
        return $url.route($routeName, $parameters, false);
    }

    return route($routeName, $parameters);
}
