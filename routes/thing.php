<?php

Route::post('/thing/add','ThingController@addTask')->middleware('token');
Route::post('/thing/accept','ThingController@acceptTask')->middleware('token');
Route::post('/thing/finish','ThingController@finishTask');
Route::get('/thing/list','ThingController@showTaskList');
Route::get('/thing/show/{task_id}','ThingController@showTaskById');
Route::post('/thing/user','ThingController@showUserList');