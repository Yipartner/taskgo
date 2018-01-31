<?php
/**
 * Created by PhpStorm.
 * User: andyhui
 * Date: 18-1-27
 * Time: 下午2:30
 */

Route::post('/user/register', 'UserController@register');
Route::post('/user/login', 'UserController@login');
Route::post('/user/resetPassword', 'UserController@resetPassword');
Route::get('/user/info/show/{userId}', 'UserController@getUserInfo');
Route::post('/user/info/update', 'UserController@updateUserInfo');
Route::post('/user/bind', 'UserController@bindLoginAccount');
Route::post('/user/info/addAuth', 'UserController@addAuthInfo');
Route::post('/user/info/updateStatus', 'UserController@updateAuthStatus');
Route::post('/user/info/updateLevel', 'UserController@updateLevel');

Route::post('/user/test', 'UserController@test');