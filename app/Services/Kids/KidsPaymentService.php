<?php

namespace App\Services\Kids;

use App\Models\Kids;
use App\Models\Kassa;
use App\Models\Payment;
use App\Models\KidsHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KidsPaymentService{

    public function createPayment($request, int $kid_id){
        
        return DB::transaction(function () use ($request, $kid_id) {
            $kids   = Kids::findOrFail($kid_id);
            $kassa  = Kassa::firstOrFail();
            $userId = auth()->id();
            $amount = (int) $request->amount;
            $isReturnCash = $request->payment_type === 'return_cash';
            $isReturnCard = $request->payment_type === 'return_card';
            if ($isReturnCash && $kassa->cash < $amount) {
                throw ValidationException::withMessages(['amount' => '']);
            }
            if ($isReturnCard && $kassa->card < $amount) {
                throw ValidationException::withMessages(['amount' => '']);
            }
            if (($isReturnCash || $isReturnCard) && $kids->balance < $amount) {
                throw ValidationException::withMessages(['amount' => '']);
            }
            $paymentType = ($isReturnCash || $isReturnCard) ? 'return' : $request->payment_type;
            $status = in_array($request->payment_type, ['cash', 'return_cash', 'return_card']) ? 'success' : 'pedding';
            $description = $request->description;
            if ($isReturnCash) $description .= ' (Naqd qaytarildi)';
            if ($isReturnCard) $description .= ' (Karta orqali qaytarildi)';
            $payment = Payment::create([
                'kid_id'         => $kids->id,
                'amount'         => $amount,
                'payment_type'   => $paymentType,
                'description'    => $description,
                'kassir_user_id' => $userId,
                'status'         => $status,
            ]);
            if ($isReturnCash || $isReturnCard) {
                $kids->decrement('balance', $amount);
                $isReturnCash ? $kassa->decrement('cash', $amount) : $kassa->decrement('card', $amount);
                KidsHistory::create([
                    'kids_id'     => $kids->id,
                    'type'        => $isReturnCash ? 'return_cash_pay' : 'return_card_pay',
                    'amount'      => $amount,
                    'payment_id'  => $payment->id,
                    'description' => $description,
                    'user_id'     => $userId,
                ]);
            }
            if ($request->payment_type === 'cash') {
                $kids->increment('balance', $amount);
                $kassa->increment('cash', $amount);
                KidsHistory::create([
                    'kids_id'     => $kids->id,
                    'type'        => 'cash_pay',
                    'amount'      => $amount,
                    'payment_id'  => $payment->id,
                    'description' => $description,
                    'user_id'     => $userId,
                ]);
            }
            return $payment;
        });



    }
}
