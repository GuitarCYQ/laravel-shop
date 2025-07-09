<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 添加白名单 url 能够匹配到 $except 里的任意一项，laravel就不会去查 CSRF_TOKEN
        'payment/alipay/notify',
        'payment/wechat/notify'
    ];
}
