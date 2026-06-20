<?php
use App\Http\Controllers\CartItemsController;
use Illuminate\Support\Facades\Route;

Route::get('/cart', [CartItemsController::class, 'index'])->middleware('auth:sanctum');
Route::post('/cart', [CartItemsController::class, 'store'])->middleware('auth:sanctum');
Route::patch('/cart', [CartItemsController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/cart/{product_id}', [CartItemsController::class, 'destroy'])->middleware('auth:sanctum');


