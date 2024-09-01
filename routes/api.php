<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout');
        Route::post('/logout-all-devices', 'logoutAllDevices');
    });

    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
});

Route::apiResource('posts', PostController::class)->only(['index', 'show']);
