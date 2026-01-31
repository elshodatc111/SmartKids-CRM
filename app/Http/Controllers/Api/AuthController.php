<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
    * @authenticated
    */
    // Login - Token olish
    public function login(Request $request) {
        $request->validate([
            'phone' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Telefon yoki parol xato'], 401);
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user
        ]);
    }

    // Parolni unutganda SMS kod yuborish
    public function forgotPassword(Request $request) {
        $request->validate(['phone' => 'required|exists:users,phone']);

        $code = rand(100000, 999999);
        Cache::put('otp_' . $request->phone, $code, now()->addMinutes(5));

        // Kelajakda: SmsService::send($request->phone, $code);
        
        return response()->json([
            'message' => 'Tasdiqlash kodi yuborildi',
            'debug_code' => $code 
        ]);
    }

    // OTP kodni tekshirish
    public function verifyOtp(Request $request) {
        $request->validate([
            'phone' => 'required|exists:users,phone',
            'code' => 'required|numeric'
        ]);

        if (Cache::get('otp_' . $request->phone) != $request->code) {
            return response()->json(['message' => 'Kod noto‘g‘ri yoki muddati o‘tgan'], 400);
        }

        $resetToken = Str::random(60);
        Cache::put('reset_token_' . $request->phone, $resetToken, now()->addMinutes(10));

        return response()->json(['reset_token' => $resetToken]);
    }

    // Yangi parolni saqlash
    public function resetPassword(Request $request) {
        $request->validate([
            'phone' => 'required|exists:users,phone',
            'reset_token' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        if (Cache::get('reset_token_' . $request->phone) != $request->reset_token) {
            return response()->json(['message' => 'Token xato yoki muddati o‘tgan'], 403);
        }

        User::where('phone', $request->phone)->update([
            'password' => Hash::make($request->password)
        ]);

        Cache::forget('otp_' . $request->phone);
        Cache::forget('reset_token_' . $request->phone);

        return response()->json(['message' => 'Parol yangilandi']);
    }

    // Tizimdan chiqish
    /**
     * @authenticated
     */
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Tizimdan chiqildi']);
    }
}