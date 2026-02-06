<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\GroupKids;
use App\Models\KidsHistory;

class AutoUpdateService{
    public function ChildPayRun(): void{
        $currentMonth = now()->format('Y-m');
        Log::info("Guruh oylik to‘lovi tekshiruvi boshlandi: {$currentMonth}");
        DB::transaction(function () use ($currentMonth) {
            $items = GroupKids::with([
                    'group:id,amount',
                    'kid:id,balance'
                ])->where('status', 'active')
                ->where(function ($q) use ($currentMonth) {
                    $q->whereNull('payment_month')
                      ->orWhere('payment_month', '!=', $currentMonth);
                })->lockForUpdate()->get();
            foreach ($items as $item) {
                if (! $item->group || ! $item->kid) {
                    continue;
                }
                $amount = $item->group->amount;
                $item->kid->decrement('balance', $amount);
                $item->update(['payment_month' => $currentMonth,]);
                KidsHistory::create([
                    'kids_id'     => $item->kids_id,
                    'type'        => 'group_pay',
                    'amount'      => $amount,
                    'group_id'    => $item->group_id,
                    'description' => "{$currentMonth} oy uchun guruh to‘lovi yechildi",
                    'user_id'     => 1, 
                ]);
            }
        });
        Log::info("Guruh oylik to‘lovi yakunlandi: {$currentMonth}");
    }
}
