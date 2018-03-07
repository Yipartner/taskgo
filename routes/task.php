<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 18/3/7
 * Time: 下午12:18
 */
Route::post('/me/task','TaskController@showTaskByUserAndStatus')->middleware('token');