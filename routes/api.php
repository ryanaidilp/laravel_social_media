<?php

use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PostController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(FollowController::class)->group(function () {
        Route::post('follow', 'follow');
        Route::post('unfollow', 'unfollow');
    });
    Route::controller(PostController::class)->prefix('post')->group(function () {
        Route::post('create', 'create');
    });
});
