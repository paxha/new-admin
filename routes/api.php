<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', 'Api\UserController@login');

Route::get('logout', 'Api\UserController@logout');

Route::middleware('auth:api')->group(function () {
    Route::get('user', 'Api\UserController@user');
    
    Route::get('modules', 'Api\ModuleController@index');
    
    Route::get('categories', 'Api\CategoryController@index');
    Route::post('category/create', 'Api\CategoryController@store');
    Route::get('category/{category}/edit', 'Api\CategoryController@edit');
    Route::put('category/{category}/update', 'Api\CategoryController@update');
    Route::get('category/{category}/toggle-active', 'Api\CategoryController@toggleActive');
    Route::delete('category/{category}/delete', 'Api\CategoryController@destroy');
    Route::delete('categories/delete', 'Api\CategoryController@destroyMany');
});
