<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PostsController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::prefix('posts')
    ->middleware('auth:api')
    ->controller(PostsController::class)
    ->group(function() {
        Route::post('/', 'getPosts');
        Route::post('/{id}/like', 'like');
        Route::post('/{id}/unlike', 'unlike');
        Route::post('/{id}', 'getPost');
    });

/**
    Route::prefix('users')->middleware('auth:sanctum')->group(function() {
        Route::post('/', );
        Route::post('/{id}/follow');
        Route::post('/{id}/unfollow');
        Route::post('/{id}');
    });
*/

