<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('users', 'UsersController@index');

    $router->get('products', 'ProductsController@index');
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');

    $router->get('orders', 'OrdersController@index')->name('orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('orders.show');
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('orders.ship');
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('orders.handle_refund');

    $router->get('coupon_codes', 'CouponCodesController@index');
    $router->get('coupon_codes/create', 'CouponCodesController@create');
    $router->post('coupon_codes', 'CouponCodesController@store');
    $router->get('coupon_codes/{id}/edit', 'CouponCodesController@edit');
    $router->put('coupon_codes/{id}', 'CouponCodesController@update');
    $router->delete('coupon_codes/{id}', 'CouponCodesController@destroy');



});
