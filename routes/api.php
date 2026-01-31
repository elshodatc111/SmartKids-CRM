<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

// Ochiq yo'llar
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // SMS yuborish mantiqi uchun
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Himoyalangan yo'llar (Faqat token bilan)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']); // Profilni ko'rish
    Route::post('/profile/update', [ProfileController::class, 'update']); // Ma'lumotlarni yangilash
    Route::post('/profile/image', [ProfileController::class, 'updateImage']); // Rasm yuklash
    Route::post('/profile/password', [ProfileController::class, 'updatePassword']); // Parolni yangilash
    Route::post('/logout', [AuthController::class, 'logout']); // Tizimdan chiqish
});
