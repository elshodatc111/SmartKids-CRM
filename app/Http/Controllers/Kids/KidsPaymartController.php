<?php

namespace App\Http\Controllers\Kids;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\Kassa;
use App\Models\Kids;
use App\Models\Payment;
use App\Models\KidsHistory;
use App\Http\Requests\Kids\KidsPaymartRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KidsPaymartController extends Controller{
    public function create(KidsPaymartRequest $request, int $kid_id){
        return DB::transaction(function () use ($request, $kid_id) {
            $kids   = Kids::findOrFail($kid_id);
            $kassa  = Kassa::firstOrFail();
            $userId = auth()->id();
            $isReturnCash = $request->payment_type === 'return_cash';
            $isReturnCard = $request->payment_type === 'return_card';
            $amount = (int) $request->amount;
            if ($isReturnCash && $kassa->cash < $amount) {throw ValidationException::withMessages(['amount' => "Kassada naqd mablag‘ yetarli emas",]);}
            if ($isReturnCard && $kassa->card < $amount) {throw ValidationException::withMessages(['amount' => "Kassada karta mablag‘i yetarli emas",]);}
            $paymentType = ($isReturnCash || $isReturnCard) ? 'return' : $request->payment_type;
            $status = in_array($request->payment_type, ['cash', 'return_cash', 'return_card']) ? 'success' : 'pedding';
            $description = $request->description;
            if ($isReturnCash) $description .= ' (Naqd qaytarildi)';
            if ($isReturnCard) $description .= ' (Karta orqali qaytarildi)';
            $payment = Payment::create([
                'kid_id'         => $kids->id,
                'amount'         => $request->amount,
                'payment_type'   => $paymentType,
                'description'    => $description,
                'kassir_user_id' => $userId,
                'status'         => $status,
            ]);
            if($request->payment_type=='return_cash' || $request->payment_type =='return_card'){
                $kids->decrement('balance', $request->amount);
                if($request->payment_type == 'return_cash'){
                    $kassa->cash = $kassa->cash - $request->amount;
                }else{
                    $kassa->card = $kassa->card - $request->amount;
                }
                $kassa->save();
                KidsHistory::create([ // 'cash_pay','card_pay','bank_pay','discount_add','return_bank_pay','group_add','group_pay','group_delte','vizited'
                    'kids_id'     => $kids->id,
                    'type'        => $request->payment_type == 'return_cash'?"return_cash_pay":"return_card_pay",
                    'amount'      => $request->amount,
                    'payment_id'  => $payment->id,
                    'description' => $description,
                    'user_id'     => $userId,
                ]);
            }
            if ($request->payment_type === 'cash') {
                $kids->increment('balance', $request->amount);
                $kassa->increment('cash', $request->amount);
                KidsHistory::create([
                    'kids_id'     => $kids->id,
                    'type'        => 'cash_pay',
                    'amount'      => $request->amount,
                    'payment_id'  => $payment->id,
                    'description' => $description,
                    'user_id'     => $userId,
                ]);
            }
            return response()->json([
                'message' => "To‘lov muvaffaqiyatli amalga oshirildi.",
                'payment' => $payment,
            ], 200);
        });
    }

}
