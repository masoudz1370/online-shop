<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminAuthController;
use App\Models\User;
use App\Http\Controllers\User\UserController;

Route::post('/auth/send-otp', [RegisterController::class, 'sendOtp']);
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

Route::post('/auth/complete-registration', [RegisterController::class, 'completeRegistration']);

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout']);