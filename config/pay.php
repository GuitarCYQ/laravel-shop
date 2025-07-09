<?php

use Yansongda\Pay\Pay;

return [
    'alipay' => [
        'default' => [
            // 「必填」支付宝分配的 app_id
            'app_id' =>  env('ALIPAY_APP_ID'),
            // 「必填」应用私钥 字符串或路径
            'app_secret_cert' => env('ALIPAY_SECRET_CERT'),
            // 设置应用私钥后，即可下载得到以
            //「必填」应用公钥证书 路径,下3个证书
            'app_public_cert_path' => storage_path('cert/appPublicCert.crt'),
            // 「必填」支付宝公钥证书 路径
            'alipay_public_cert_path' => storage_path('cert/alipayPublicCert.crt'),
            // 「必填」支付宝根证书 路径
            'alipay_root_cert_path' => storage_path('cert/alipayRootCert.crt'),
            // 前端页面的回调，可以不填，在AppServiceProvider.php里面可以直接写
            'return_url' => '',
             // 服务器端的回调，可以不填，在AppServiceProvider.php里面可以直接写
            'notify_url' => '',

            // 「选填」默认为正常模式。可选为： 沙盒MODE_SANDBOX 正式MODE_NORMAL, MODE_SERVICE
            'mode' => Pay::MODE_NORMAL,

            // 「选填」第三方应用授权token
            'app_auth_token' => '',
            // 「选填」服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
            'service_provider_id' => '',

        ]
    ],
    'wechat' => [
        'default' => [
            // 「必填」商户号，服务商模式下为服务商商户号
            // 可在 https://pay.weixin.qq.com/ 账户中心->商户信息 查看
            'mch_id' => '',
            // 「选填」v2商户私钥
            'mch_secret_key_v2' => '',
            // 「必填」v3 商户秘钥
            // 即 API v3 密钥(32字节，形如md5值)，可在 账户中心->API安全 中设置
            'mch_secret_key' => '',
            // 「必填」商户私钥 字符串或路径
            // 即 API证书 PRIVATE KEY，可在 账户中心->API安全->申请API证书 里获得
            // 文件名形如：apiclient_key.pem
            'mch_secret_cert' => '',
            // 「必填」商户公钥证书路径
            // 即 API证书 CERTIFICATE，可在 账户中心->API安全->申请API证书 里获得
            // 文件名形如：apiclient_cert.pem
            'mch_public_cert_path' => '',
            // 「必填」微信回调url
            // 不能有参数，如?号，空格等，否则会无法正确回调
            'notify_url' => 'http://shop.test/wechat/notify',
            // 「选填」公众号 的 app_id
            // 可在 mp.weixin.qq.com 设置与开发->基本配置->开发者ID(AppID) 查看
            'mp_app_id' => '2016082000291234',
            // 「选填」小程序 的 app_id
            'mini_app_id' => '',
            // 「选填」app 的 app_id
            'app_id' => '',
            // 「选填」服务商模式下，子公众号 的 app_id
            'sub_mp_app_id' => '',
            // 「选填」服务商模式下，子 app 的 app_id
            'sub_app_id' => '',
            // 「选填」服务商模式下，子小程序 的 app_id
            'sub_mini_app_id' => '',
            // 「选填」服务商模式下，子商户id
            'sub_mch_id' => '',
            // 「选填」（适用于 2024-11 及之前开通微信支付的老商户）微信支付平台证书序列号及证书路径，强烈建议 php-fpm 模式下配置此参数
            // 「必填」微信支付公钥ID及证书路径，key 填写形如 PUB_KEY_ID_0000000000000024101100397200000006 的公钥id，见 https://pay.weixin.qq.com/doc/v3/merchant/4013053249
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
                'PUB_KEY_ID_0000000000000024101100397200000006' => __DIR__.'/Cert/publickey.pem',
            ],
            // 「选填」默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
            'mode' => Pay::MODE_NORMAL,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => './logs/pay.log',
        'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
        'type' => 'single', // optional, 可选 daily.
        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
];
