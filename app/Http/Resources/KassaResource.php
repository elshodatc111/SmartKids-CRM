<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KassaResource extends JsonResource{
    public function toArray($request){
        return [
            'balance' => [
                'cash' => $this->cash,
                'card' => $this->card,
                'bank' => $this->bank,
            ],
            'out' => [
                'cash' => $this->out_cash_pending,
                'card' => $this->out_card_pending,
                'bank' => $this->out_bank_pending,
            ],
            'cost' => [
                'cash' => $this->cost_cash_pending,
                'card' => $this->cost_card_pending,
                'bank' => $this->cost_bank_pending,
            ],
        ];
    }
}
