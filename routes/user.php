<?php
/**
 * Created by PhpStorm.
 * User: andyhui
 * Date: 18-1-27
 * Time: 下午2:30
 */

Route::post('/user/register', 'UserController@register');
Route::post('/user/login', 'UserController@login');

Route::get('/user/token/check','UserController@checkToken');
Route::get('/user/getCaptcha/{mobile}','UserController@sendMessage');
Route::post('/user/forgotPassword','UserController@forgotPassword');

Route::group(['middleware' => 'token'], function() {
    Route::get('/user/logout', 'UserController@logout');
    Route::post('/user/resetPassword', 'UserController@resetPassword');

    Route::get('/user/info/show/', 'UserController@getUserInfo');
    Route::post('/user/info/update', 'UserController@updateUserInfo');
    Route::post('/user/info/addAuth', 'UserController@addAuthInfo');
    Route::get('/user/info/updateStatus', 'UserController@updateAuthStatus');
    Route::post('/user/info/updateLevel', 'UserController@updateLevel');
    Route::get('/user/info/show/{user_id}', 'UserController@getUserInfoById');

    Route::post('/user/bind', 'UserController@bindLoginAccount');

    Route::get('/user/getFollowers', 'UserController@getFollowers');
    Route::get('/user/getFollowings', 'UserController@getFollowings');
    Route::get('/user/follow/{follower_id}', 'UserController@followUser');
    Route::get('/user/unFollow/{follower_id}', 'UserController@unFollowUser');
});