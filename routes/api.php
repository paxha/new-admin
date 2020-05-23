<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', 'UserController@login');

Route::get('logout', 'UserController@logout');

Route::post('upload-image', 'ImageController@upload');
Route::post('remove-image', 'ImageController@remove');

Route::middleware('auth:api')->group(function () {
    Route::get('user', 'UserController@user');

    Route::get('modules', 'ModuleController@index');

    /*
     * Category Apis
     * */
    Route::get('categories', 'CategoryController@index');
    Route::post('category/create', 'CategoryController@store');
    Route::get('category/{category}/edit', 'CategoryController@edit');
    Route::put('category/{category}/update', 'CategoryController@update');
    Route::get('category/{category}/toggle-active', 'CategoryController@toggleActive');
    Route::delete('category/{category}/delete', 'CategoryController@destroy');
    Route::delete('categories/delete', 'CategoryController@destroyMany');

    /*
     * Unit Apis
     * */
    Route::get('units', 'UnitController@index');
    Route::post('unit/create', 'UnitController@store');
    Route::get('unit/{unit}/edit', 'UnitController@edit');
    Route::put('unit/{unit}/update', 'UnitController@update');
    Route::get('unit/{unit}/toggle-active', 'UnitController@toggleActive');
    Route::delete('unit/{unit}/delete', 'UnitController@destroy');
    Route::delete('units/delete', 'UnitController@destroyMany');

    /*
     * Attribute Apis
     * */
    Route::get('attributes', 'AttributeController@index');
    Route::post('attribute/create', 'AttributeController@store');
    Route::get('attribute/{attribute}/edit', 'AttributeController@edit');
    Route::put('attribute/{attribute}/update', 'AttributeController@update');
    Route::get('attribute/{attribute}/toggle-active', 'AttributeController@toggleActive');
    Route::delete('attribute/{attribute}/delete', 'AttributeController@destroy');
    Route::delete('attributes/delete', 'AttributeController@destroyMany');

    /*
     * Attribute Apis
     * */
    Route::get('brands', 'BrandController@index');
    Route::post('brand/create', 'BrandController@store');
    Route::get('brand/{brand}/edit', 'BrandController@edit');
    Route::put('brand/{brand}/update', 'BrandController@update');
    Route::get('brand/{brand}/toggle-popular', 'BrandController@togglePopular');
    Route::get('brand/{brand}/toggle-active', 'BrandController@toggleActive');
    Route::delete('brand/{brand}/delete', 'BrandController@destroy');
    Route::delete('brands/delete', 'BrandController@destroyMany');
});
