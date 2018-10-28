<?php

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

Route::get('/', function () {
    return view('welcome');
});

// 获取手机验证码
Route::post('user/getcode', 'ShortMessage\SendMessageController@sendMessage');
// 用户注册
Route::post('user/register', 'Auth\RegisterController@register');
// 用户登录
Route::post('user/login', 'Auth\LoginController@login');
// 用户上传图片
Route::post('/upload', 'Upload\UploadPictureController@uploadPicture');

// 用户添加卡包
Route::post('package/add', 'Package\AddPackageController@addPackage');
// 用户删除卡包
Route::post('package/delete', 'Package\RemovePackageController@removePackage')->middleware('auth');
// 用户更新卡包
Route::post('package/update', 'Package\ModifyPackageController@modifyPackage')->middleware('auth');
// 用户查找自己的所有卡包信息
Route::post('package/find', 'Package\FindPackageController@findPackage')->middleware('auth');
