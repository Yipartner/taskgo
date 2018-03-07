<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 18/3/7
 * Time: 下午12:18
 */
Route::post('/me/accepttask','TaskController@showAcceptTaskByUserAndStatus')->middleware('token');
Route::post('/me/finishtask','TaskController@showTaskByUserAndStatus')->middleware('token');