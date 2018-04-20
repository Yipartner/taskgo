<?php

Route::get('/me/cards','CardController@getMyCards')->middleware('token');
Route::post('/cards','CardController@createCard');
Route::put('/cards','CardController@updateCard');
Route::get('/cards','CardController@getAllCard');