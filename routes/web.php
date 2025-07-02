<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// /路径下指定的是PagesController里的root方法，别名为root
Route::get('/','PagesController@root')->name('root')->middleware('verified');

// Laravel 的用户认证路由
Auth::routes(['verify' => true]);

//auth组件间代表需要登录，verified组件间代表需要经过邮箱验证
Route::group(['middleware' => ['auth','verified']],function() {
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
    Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
    Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
    route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
    route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
    route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');
});

