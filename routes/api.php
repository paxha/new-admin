<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', 'Api\UserController@login');

Route::get('logout', 'Api\UserController@logout');

Route::get('modules', 'Api\ModuleController@index');

Route::middleware('auth:api')->group(function () {
    Route::get('user', 'Api\UserController@user');
});
