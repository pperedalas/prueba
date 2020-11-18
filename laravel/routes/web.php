<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\ApiAuthMiddleware as ApiAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// UserController routes
Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
Route::put('/user/update', [UserController::class, 'update']);
Route::post('/user/upload', [UserController::class, 'upload'])->middleware(ApiAuthMiddleware::class);
Route::get('/user/avatar/{filename}', [UserController::class, 'getImage']);
Route::get('/user/detail/{id}', [UserController::class, 'detail']);

// CategoryController routes
Route::resource('/category', CategoryController::class);

// PostController routes
Route::resource('/post', PostController::class);
Route::post('/post/upload', [PostController::class, 'upload'])->middleware(ApiAuthMiddleware::class);
Route::get('/post/image/{filename}', [PostController::class, 'getImage']);
Route::get('/post/category/{id}', [PostController::class, 'getPostsByCategory']);
Route::get('/post/user/{id}', [PostController::class, 'getPostsByUser']);