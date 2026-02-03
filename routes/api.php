<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Finance\FinanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kassa\KassaController;

// Ochiq yo'llar
Route::post('/login', [AuthController::class, 'login']);

// Himoyalangan yo'llar (Faqat token bilan)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']); // Profilni ko'rish
    Route::post('/profile/update', [ProfileController::class, 'update']); // Ma'lumotlarni yangilash
    Route::post('/profile/image', [ProfileController::class, 'updateImage']); // Rasm yuklash
    Route::post('/profile/password', [ProfileController::class, 'updatePassword']); // Parolni yangilash
    Route::post('/logout', [AuthController::class, 'logout']); // Tizimdan chiqish
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/finance', [FinanceController::class, 'getFinance']);
    Route::get('/finance/histories', [FinanceController::class, 'getFinanceHistory']);

});

Route::middleware(['auth:sanctum'])->prefix('kassa')->group(function () {
    Route::get('/get', [KassaController::class, 'getKassa']);
    Route::post('/pedding', [KassaController::class, 'pendingKassa']);
    Route::get('/success/{id}', [KassaController::class, 'successKassa']);
    Route::get('/cancel/{id}', [KassaController::class, 'cancelKassaTransaction']);
    Route::middleware('admin')->group(function () {
        Route::post('/approve', [KassaController::class, 'approve']);
        Route::post('/cancel', [KassaController::class, 'cancel']);
    });
});