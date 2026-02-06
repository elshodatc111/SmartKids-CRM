<?php

namespace App\Services\Kids;

use App\Models\Kids;
use App\Models\Kassa;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\KidsHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\SmsService;

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

    public function listByKid(int $kids_id){
        return Payment::with(['kassir:id,name','successAdmin:id,name'])
            ->where('kid_id', $kids_id)
            ->orderByDesc('id')
            ->get([
                'id',
                'kid_id',
                'amount',
                'payment_type',
                'status',
                'description',
                'kassir_user_id',
                'success_admin_id',
                'created_at',
                'updated_at',
            ])->map(function ($payment) {
                return [
                    'id'           => $payment->id,
                    'amount'       => $payment->amount,
                    'payment_type' => $payment->payment_type,
                    'status'       => $payment->status,
                    'description'  => $payment->description,
                    'kassir'       => optional($payment->kassir)->name,
                    'admin'        => optional($payment->successAdmin)->name,
                    'created_at'   => $payment->created_at,
                    'updated_at'   => $payment->updated_at,
                ];
            });
    }

    public function allPaymarts(){
        return Payment::with(['kassir:id,name','successAdmin:id,name','kid:id,full_name'])
            ->orderByDesc('id')
            ->get([
                'id',
                'kid_id',
                'amount',
                'payment_type',
                'status',
                'description',
                'kassir_user_id',
                'success_admin_id',
                'created_at',
                'updated_at',
            ])->map(function ($payment) {
                return [
                    'id'           => $payment->id,
                    'kid_id'       => $payment->kid_id,
                    'kid_name'     => optional($payment->kid)->full_name,
                    'amount'       => $payment->amount,
                    'payment_type' => $payment->payment_type,
                    'status'       => $payment->status,
                    'description'  => $payment->description,
                    'kassir'       => optional($payment->kassir)->name,
                    'admin'        => optional($payment->successAdmin)->name,
                    'created_at'   => $payment->created_at,
                    'updated_at'   => $payment->updated_at,
                ];
            });
    }

    public function confirmPayment(int $id){
        return DB::transaction(function () use ($id) {
            $paymart = Payment::findOrFail($id);
            if ($paymart->status == 'success') {
                throw ValidationException::withMessages([
                    'payment' => "Bu to'lov oldin tasdiqlangan",
                ]);
            }
            if ($paymart->status == 'cancel') {
                throw ValidationException::withMessages([
                    'payment' => "Bu to'lov oldin bekor qilingan",
                ]);
            }
            if ($paymart->payment_type == 'return') {
                throw ValidationException::withMessages([
                    'payment' => "Qaytarilgan to'lov avtomatik tasdiqlanadi",
                ]);
            }
            $paymart->status = 'success';
            $paymart->success_admin_id = auth()->user()->id;
            $paymart->save();
            $kids = Kids::findOrFail($paymart->kid_id);
            $kids->increment('balance', $paymart->amount);
            $kassa = Kassa::firstOrFail();
            if ($paymart->payment_type == 'cash') {
                $kassa->increment('cash', $paymart->amount);
            }
            if ($paymart->payment_type == 'card') {
                $kassa->increment('card', $paymart->amount);
            }
            if ($paymart->payment_type == 'bank') {
                $kassa->increment('bank', $paymart->amount);
            }
            KidsHistory::create([
                'kids_id'     => $paymart->kid_id,
                'amount'      => $paymart->amount,
                'type'        => $paymart->payment_type == 'cash'
                                    ? "cash_pay"
                                    : ($paymart->payment_type == 'card'
                                        ? "card_pay"
                                        : ($paymart->payment_type == 'bank'
                                            ? "bank_pay"
                                            : 'discount_add')),
                'payment_id'  => $paymart->id,
                'description' => $paymart->description,
                'user_id'     => $paymart->kassir_user_id,
            ]);
            return "To'lov tasdiqlandi";
        });
    }

    public function kidsPaymartCancel($id){
        return DB::transaction(function () use ($id) {
            $paymart = Payment::findOrFail($id);
            if ($paymart->status == 'success') {
                throw ValidationException::withMessages([
                    'payment' => "Bu to'lov oldin tasdiqlangan",
                ]);
            }
            if ($paymart->status == 'cancel') {
                throw ValidationException::withMessages([
                    'payment' => "Bu to'lov oldin bekor qilingan",
                ]);
            }
            if ($paymart->payment_type == 'return') {
                throw ValidationException::withMessages([
                    'payment' => "Qaytarilgan to'lov avtomatik tasdiqlanadi",
                ]);
            }
            $paymart->status = 'cancel';
            $paymart->success_admin_id = auth()->user()->id;
            $paymart->save();
            return true;
        });
    }
    

}
