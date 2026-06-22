<?php

use App\Http\Controllers\OrderController;

Route::get('/order', [OrderController::class, 'index'])->middleware('auth:sanctum');
Route::get('/order/{order_id}', [OrderController::class, 'show'])->middleware('auth:sanctum');
Route::post('/order', [OrderController::class, 'store'])->middleware('auth:sanctum');
Route::patch('/order', [OrderController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/order/{order_id}', [OrderController::class, 'destroy'])->middleware('auth:sanctum');

