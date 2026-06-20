<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use App\Http\Controllers\User\UserController;
require __DIR__ . '/api/users.php';
require __DIR__ . '/api/auth.php';
require __DIR__ . '/api/products.php';
require __DIR__ . '/api/cartItems.php';
require __DIR__ . '/api/logs.php';




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/ptest', function () {
    return "asdasd";
});

/*Route::post('/auth/send-otp', [RegisterController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [RegisterController::class, 'verifyOtp']);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
});

Route::post('/auth/complete-registration', [RegisterController::class, 'completeRegistration']);*/
