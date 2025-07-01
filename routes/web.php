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

