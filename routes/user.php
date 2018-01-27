<?php
/**
 * Created by PhpStorm.
 * User: andyhui
 * Date: 18-1-27
 * Time: 下午2:30
 */

Route::post('/user/register', 'UserController@register');
Route::post('/user/login', 'UserController@login');
Route::post('/user/test', 'UserController@test');