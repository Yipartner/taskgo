<?php

Route::post('/thing/add','ThingController@addTask');
Route::post('/thing/accept','ThingController@acceptTask');
Route::post('/thing/finish','ThingController@finishTask');
Route::get('/thing/list','ThingController@showTaskList');
Route::get('/thing/show/{task_id}','ThingController@showTaskById');