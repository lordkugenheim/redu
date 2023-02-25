<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('api')->group(function() {

    Route::prefix('posts')->middleware('auth:sanctum')->group(function() {
        Route::post('/', );
        Route::post('/{id}/like');
        Route::post('/{id}/unlike');
        Route::post('/{id}');
    });

    Route::prefix('users')->middleware('auth:sanctum')->group(function() {
        Route::post('/', );
        Route::post('/{id}/follow');
        Route::post('/{id}/unfollow');
        Route::post('/{id}');
    });

});
