<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\FinanceHistory;

class KassaResource extends JsonResource{
    public function toArray($request){
        $pending = FinanceHistory::with('user:id,name')->whereNull('admin_id')->get();
        $sum = function ($type, $reason) use ($pending) {
            return (float) $pending->where('type', $type)->where('reason', $reason)->sum('amount');
        };
        $items = function ($type, $reason) use ($pending) {
            return $pending->where('type', $type)->where('reason', $reason)->values()->map(fn ($row) => [
                    'id'          => $row->id,
                    'amount'      => $row->amount,
                    'description' => $row->description,
                    'user'        => [
                        'id'   => $row->user_id,
                        'name' => $row->user?->name,
                    ],
                    'created_at'  => $row->created_at?->toDateTimeString(),
                ]);
        };
        return [
            'balance' => [
                'cash' => $this->cash,
                'card' => $this->card,
                'bank' => $this->bank,
            ],
            'out' => [
                'total' => [
                    'cash' => $this->out_cash_pending + $sum('cash', 'kirim'),
                    'card' => $this->out_card_pending + $sum('card', 'kirim'),
                    'bank' => $this->out_bank_pending + $sum('bank', 'kirim'),
                ],
                'items' => [
                    'cash' => $items('cash', 'kirim'),
                    'card' => $items('card', 'kirim'),
                    'bank' => $items('bank', 'kirim'),
                ],
            ],
            'cost' => [
                'total' => [
                    'cash' => $this->cost_cash_pending + $sum('cash', 'xarajat'),
                    'card' => $this->cost_card_pending + $sum('card', 'xarajat'),
                    'bank' => $this->cost_bank_pending + $sum('bank', 'xarajat'),
                ],
                'items' => [
                    'cash' => $items('cash', 'xarajat'),
                    'card' => $items('card', 'xarajat'),
                    'bank' => $items('bank', 'xarajat'),
                ],
            ],
        ];
    }
}
