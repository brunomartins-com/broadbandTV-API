<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function() {

    Route::get('users', 'UserController@getUsers');
    Route::post('user/add', 'UserController@postAdd');
    Route::get('key-error', 'UserController@showKeyError');

    Route::get('recipes', 'RecipeController@getRecipes')->middleware('verifyKey');

});