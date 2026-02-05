<?php

namespace App\Http\Controllers\Kids;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\User;
use App\Models\Kassa;
use App\Models\Kids;
use App\Models\Payment;
use App\Models\KidsHistory;
use App\Http\Requests\Kids\KidsPaymartRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Kids\KidsPaymentService;

class KidsPaymartController extends Controller{
    protected KidsPaymentService $paymentService;

    public function __construct(KidsPaymentService $paymentService){$this->paymentService = $paymentService;}

    public function create(KidsPaymartRequest $request, int $kid_id){
        $payment = $this->paymentService->createPayment($request, $kid_id);
        return response()->json([
            'message' => "To‘lov muvaffaqiyatli amalga oshirildi.",
            'payment' => $payment,
        ], 200);
    }

    public function kidsPaymarts(int $kids_id){
        $payments = $this->paymentService->listByKid($kids_id);
        return response()->json([
            'message' => "To‘lov malumotlari.",
            'payment' => $payments,
        ], 200);
    }

    public function allPaymarts(){
        $payments = $this->paymentService->allPaymarts();
        return response()->json([
            'message' => "To‘lov malumotlari.",
            'payment' => $payments,
        ], 200);
    }

    public function kidsPaymartSuccess($id){
        $result = $this->paymentService->confirmPayment($id);
        return response()->json([
            'message' => $result,
        ], 200);
    }

    public function kidsPaymartCancel($id){
        $this->paymentService->kidsPaymartCancel($id);
        return response()->json([
            'message' => "To'lov bekor qilindi",
        ], 200);
    }



}
