<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/admin/product', [ProductController::class, 'store']);
Route::patch('/admin/product/{product_id}', [ProductController::class, 'update']);
Route::delete('/admin/product/{product_id}', [ProductController::class, 'destroy']);
Route::get('/admin/product', [ProductController::class, 'index']);
Route::get('/admin/product/{product_id}', [ProductController::class, 'show']);



