<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;


class AuthService
{
    public function generateOtp(string $phone): int
    {
        $otp = rand(100000, 999999);
        $cacheKey = "otp:{$phone}";

        Cache::put($cacheKey, $otp, now()->addMinutes(2));

        return $otp;
    }
}
