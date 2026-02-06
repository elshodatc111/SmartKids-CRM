<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Finance\FinanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kassa\KassaController;
use App\Http\Controllers\Emploes\EmploesController;
use App\Http\Controllers\Kids\KidsController;
use App\Http\Controllers\Kids\KidsPaymartController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\GroupKidController;
use App\Http\Controllers\Group\GroupUserController;

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
    Route::post('/finance/output', [FinanceController::class, 'getFinanceOutput']);
    Route::post('/finance/donation-update',[FinanceController::class, 'updateDonationPercent']);
});
Route::middleware(['auth:sanctum'])->prefix('kassa')->group(function () {
    Route::get('/get', [KassaController::class, 'getKassa']);
    Route::post('/pedding', [KassaController::class, 'pendingKassa']);
    Route::post('/success/{id}', [KassaController::class, 'successKassa']);
    Route::post('/cancel/{id}', [KassaController::class, 'cancelKassaTransaction']);
});
Route::middleware(['auth:sanctum'])->prefix('emploes')->group(function () {
    Route::get('/all', [EmploesController::class, 'allEmploes']);
    Route::post('/create', [EmploesController::class, 'createEmploes']);
    Route::post('/update/{id}', [EmploesController::class, 'updateEmploes']);
    Route::post('/create/paymart/{id}', [EmploesController::class, 'createPaymart']);
    Route::get('/show/{$id}', [EmploesController::class, 'showEmploes']);
    Route::post('/update/password/{id}', [EmploesController::class, 'passwordUpdate']);
    Route::post('/create/davomad/{id}', [EmploesController::class, 'createDavomad']);
});
Route::middleware(['auth:sanctum'])->prefix('kids')->group(function () {
    Route::get('/all', [KidsController::class, 'all']);
    Route::get('/active', [KidsController::class, 'active']);
    Route::get('/isactive', [KidsController::class, 'inactive']);
    Route::post('/create', [KidsController::class, 'create']);
    Route::post('/create/photo/{id}', [KidsController::class, 'createPhoto']);
    Route::post('/create/document/{id}', [KidsController::class, 'createDocument']);
    Route::post('/create/passport/{id}', [KidsController::class, 'createPassport']);
    Route::post('/create/certificate/{id}', [KidsController::class, 'createCertificate']);  
    Route::get('/histore/{id}', [KidsController::class, 'kidsHistory']); // Bolaning tarixi qo'shilmagan
    Route::post('/create/paymart/{id}', [KidsPaymartController::class, 'create']);
    Route::get('/paymarts', [KidsPaymartController::class, 'allPaymarts']);
    Route::get('/paymart/{id}', [KidsPaymartController::class, 'kidsPaymarts']);
    Route::post('/paymart/success{id}', [KidsPaymartController::class, 'kidsPaymartSuccess']);
    Route::post('/paymart/cancel{id}', [KidsPaymartController::class, 'kidsPaymartCancel']);
});
Route::middleware(['auth:sanctum'])->prefix('group')->group(function () {
    Route::get('/all', [GroupController::class, 'all']); // +
    Route::post('/create', [GroupController::class, 'create']);  // +
    Route::post('/update/{id}', [GroupController::class, 'update']); // +
    Route::get('/kids/{id}', [GroupController::class, 'groupKids']);  // +  groupUsers
    Route::get('/users/{id}', [GroupController::class, 'groupUsers']);  // +  
    Route::get('/show/{id}', [GroupController::class, 'show']);  // Kutilmoqda
    Route::post('/add/kids', [GroupKidController::class, 'add']);  // +
    Route::post('/delete/kids/{id}', [GroupKidController::class, 'delete']);  // +
    Route::post('/add/user', [GroupUserController::class, 'add']);  // Kutilmoqda
    Route::post('/delete/user/{id}', [GroupUserController::class, 'delete']);  // Kutilmoqda
});
    
