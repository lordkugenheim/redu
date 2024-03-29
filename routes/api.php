<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\UsersController;

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

Route::controller(AuthController::class)
    ->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
});

Route::prefix('posts')
    ->middleware('auth:api')
    ->controller(PostsController::class)
    ->group(function() {
        Route::post('/', 'getPosts');
        Route::put('/', 'createPost');
        Route::post('/{id}/like', 'like');
        Route::post('/{id}/unlike', 'unlike');
        Route::post('/{id}', 'getPost');
    });

Route::prefix('users')
    ->middleware('auth:api')
    ->controller(UsersController::class)
    ->group(function() {
        Route::post('/', 'getUsers');
        Route::post('/{id}/follow', 'followUser');
        Route::post('/{id}/unfollow', 'unfollowUser');
        Route::post('/{id}', 'getUser');
    });

