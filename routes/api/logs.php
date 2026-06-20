<?php

use App\Models\CartLog;
use App\Models\Log;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


// تعریف مسیر برای مشاهده لاگ‌ها
Route::get('/cart-logs', function () {
    // گرفتن تمام رکوردها از مونگو و بازگشت به صورت JSON
    return response()->json(CartLog::all());
})->middleware('auth:sanctum');

Route::get('/logs', function () {
    // گرفتن تمام رکوردها از مونگو و بازگشت به صورت JSON
    return response()->json(Log::all());
})->middleware('auth:sanctum');

/*Route::get('/mongo-test', function () {
    return DB::connection('mongodb')
        ->collection('cart_logs')
        ->get();
});*/

/*Route::get('/cart-logs', function () {

    CartLog::create([
        'user_id' => 1,
        'action' => 'test log',
        'items' => []
    ]);

    return CartLog::all();
});*/

