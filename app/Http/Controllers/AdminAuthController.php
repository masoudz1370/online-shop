<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $admin = AdminUser::where('user', $request->user)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $plainToken = Str::random(60);

        $admin->api_token = hash('sha256', $plainToken);
        $admin->save();

        return response()->json([
            'message' => 'Login successful',
            'token' => $plainToken,
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Token not provided'
            ], 401);
        }

        $admin = AdminUser::where('api_token', hash('sha256', $token))->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        $admin->api_token = null;
        $admin->save();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
}

