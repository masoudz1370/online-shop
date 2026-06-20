<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\SendOtpRequest;
use App\Jobs\LogToMongo;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Http\Requests\Auth\RegisterRequest;


class RegisterController extends Controller
{
    protected $authService;

    // تزریق وابستگی: لاراول به صورت هوشمند کلاس سرویس را اینجا آماده می‌کند
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        //dd('sendOtp reached');
        $phone = $request->validated()['phone'];

        $cooldownKey = "otp_cooldown:{$phone}";
        $attemptKey = "otp_attempts:{$phone}";

        $attempts = Cache::get($attemptKey, 0);

        if ($attempts >= 5) {
            return response()->json([
                'message' => 'Too many OTP requests. Try again later.'
            ], 429);
        }

        if (Cache::has($cooldownKey)) {
            return response()->json([
                'message' => 'OTP already sent. Please wait before requesting again.'
            ], 429);
        }

        // فراخوانی متد تولید کد از لایه سرویس
        $otp = $this->authService->generateOtp($phone);

        Cache::put($cooldownKey, true, now()->addSeconds(90));
        Cache::put($attemptKey, $attempts + 1, now()->addHour());

        // در پروژه واقعی اینجا باید کد را SMS کنیم، فعلاً در پاسخ برمی‌گردانیم

        LogToMongo::dispatch([
            'section' => 'OTP',
            'action' => 'Send OTP Succesfull',
            'user_id' => null,
            'data' => 'OTP Send For Number: ' . $phone,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully.',
            'otp' => $otp // فقط برای تست، در محیط واقعی این خط حذف می‌شود
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $phone = $request->phone;
        $otp = $request->otp;

        $cacheKey = "otp:{$phone}";
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP منقضی شده یا پیدا نشد',
            ], 400);
        }

        if ($cachedOtp != $otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP نامعتبر است',
            ], 400);
        }

        Cache::forget($cacheKey);

        $user = User::where('phone', $phone)->first();

        // ✅ اگر کاربر وجود دارد → لاگین
        if ($user) {

            $token = $user->createToken('auth_token')->plainTextToken;

            LogToMongo::dispatch([
                'section' => 'authorization',
                'action' => 'user login succesfully',
                'user_id' => $user->id,
                'data' => [
                    'user_phone' => $user->phone,
                    'user_name' => $user->name,
                ],
            ]);

            return response()->json([
                'status' => 'success',
                'is_new_user' => false,
                'message' => 'ورود با موفقیت انجام شد',
                'token' => $token,
                'user' => $user,
            ], 200);

        }

        // ✅ اگر کاربر وجود ندارد → ثبت نام ادامه دارد
        $registrationToken = Str::random(60);

        Cache::put(
            "registration:{$registrationToken}",
            ['phone' => $phone],
            now()->addMinutes(10)
        );

        LogToMongo::dispatch([
            'section' => 'authorization',
            'action' => 'user creation request accepted',
            'user_id' => null,
            'data' => 'Use Phone is: ' . $phone,
        ]);

        return response()->json([
            'status' => 'success',
            'is_new_user' => true,
            'message' => 'کاربر جدید است، ثبت نام را تکمیل کنید',
            'registration_token' => $registrationToken,
            'phone' => $phone,
            'expires_in' => 600
        ], 200);
    }

    public function completeRegistration(RegisterRequest $request)
    {
        $registrationToken = $request->registration_token;

        $data = Cache::get("registration:{$registrationToken}");

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'توکن ثبت نام نامعتبر یا منقضی شده است'
            ], 400);
        }

        $phone = $data['phone'];

        $user = User::create([
            'phone' => $phone,
            'name' => $request->name,
            'address' => $request->address,
            'password' => bcrypt($request->password),
        ]);

        Cache::forget("registration:{$registrationToken}");

        $token = $user->createToken('auth_token')->plainTextToken;

        LogToMongo::dispatch([
            'section' => 'authorization',
            'action' => 'user created succusfully',
            'user_id' => $user->id,
            'data' => $user->toArray(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ثبت نام با موفقیت انجام شد',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

}
