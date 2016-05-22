<?php

use App\Libraries\Usda;

Route::get('/', function () {

    $usda = new Usda();
   
    $report = $usda->getIngredientInfo('01008');

    echo $report;

    //return view('welcome');
});

Route::group(['prefix' => 'api'], function() {

    Route::group(['prefix' => 'user'], function() {
        Route::get('list', 'UserController@getList');
        Route::post('add', 'UserController@postAdd');
    });

    Route::get('key-error', 'UserController@showKeyError');

    Route::group(['prefix' => 'recipe', 'middleware' => 'verifyKey'], function() {
        Route::get('list', 'RecipeController@getList');
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