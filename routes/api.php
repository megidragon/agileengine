<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'App\Http\Controllers\Auth\AuthController@login');

Route::get('search/{term}', 'App\Http\Controllers\Images\ImageController@search');

Route::prefix('images')->group(function() {
    Route::get('/', 'App\Http\Controllers\Images\ImageController@index');
    Route::get('{id}', 'App\Http\Controllers\Images\ImageController@show');
});
