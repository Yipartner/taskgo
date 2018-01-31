<?php


Route::post('/water/add','WaterTaskController@addTask');
Route::get('/water/show','WaterTaskController@showTask');
Route::get('/water/show/by/{userId}','WaterTaskController@showTaskByUser');
Route::get('/water/accept/{taskId}','WaterTaskController@acceptTask');
Route::get('/water/finish/{taskId}','WaterTaskController@finishTask');