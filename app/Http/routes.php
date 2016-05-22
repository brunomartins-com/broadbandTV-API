<?php

Route::get('/', function () {
    return json_encode(['status' => true, 'message' => '404 Error! Page not found']);
});

Route::group(['prefix' => 'api'], function() {

    Route::get('/', function () {
        return json_encode(['status' => true, 'message' => '404 Error! Page not found']);
    });

    Route::group(['prefix' => 'user'], function() {
        Route::get('list', 'UserController@getList');
        Route::post('add', 'UserController@postAdd');
    });

    Route::get('key-error', 'UserController@showKeyError');

    Route::group(['prefix' => 'recipe', 'middleware' => 'verifyKey'], function() {
        Route::get('list', 'RecipeController@getList');
        Route::get('get', 'RecipeController@getRecipe');
        Route::post('add', 'RecipeController@postAdd');
        Route::put('edit', 'RecipeController@putEdit');
        Route::delete('delete', 'RecipeController@delete');
    });

    Route::group(['prefix' => 'ingredient', 'middleware' => 'verifyKey'], function() {
        Route::get('list', 'IngredientController@getList');
        Route::post('add', 'IngredientController@postAdd');
        Route::put('edit', 'IngredientController@putEdit');
        Route::delete('delete', 'IngredientController@delete');
    });
});