<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\AuthService;
use App\Http\Requests\Auth\{LoginRequest,ResetPasswordRequest};

class AuthController extends Controller{

    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request) {
        $data = $this->authService->login(
            $request->phone,
            $request->password
        );
        return response()->json($data, 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Tizimdan chiqildi'],200);
    }
}