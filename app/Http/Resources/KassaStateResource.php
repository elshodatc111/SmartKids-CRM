<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KassaStateResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'real' => [
                'cash' => $this->cash,
                'card' => $this->card,
                'bank' => $this->bank,
            ],
            'pending' => [
                'income' => [
                    'cash' => $this->out_cash_pending,
                    'card' => $this->out_card_pending,
                    'bank' => $this->out_bank_pending,
                ],
                'cost' => [
                    'cash' => $this->cost_cash_pending,
                    'card' => $this->cost_card_pending,
                    'bank' => $this->cost_bank_pending,
                ],
            ],
            'total' => [
                'real_balance' => $this->total_balance,
                'pending_income' => $this->total_out_pending,
                'pending_cost' => $this->total_cost_pending,
            ],
        ];
    }
}
