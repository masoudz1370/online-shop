<?php

use App\Http\Controllers\OrderController;

Route::get('/order', [OrderController::class, 'index'])->middleware('auth:sactum');
Route::get('/order/{order_id}', [OrderController::class, 'show'])->middleware('auth:sactum');
Route::post('/order', [OrderController::class, 'store'])->middleware('auth:sactum');
Route::patch('/order', [OrderController::class, 'update'])->middleware('auth:sactum');
Route::delete('/order/{order_id}', [OrderController::class, 'destroy'])->middleware('auth:sactum');

