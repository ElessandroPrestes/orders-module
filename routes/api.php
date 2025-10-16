<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\V1\OrderController;

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'show']);


Route::middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);
});



Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);
});

