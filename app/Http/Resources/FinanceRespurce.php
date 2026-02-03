<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceRespurce extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'cash' => $this->cash,
            'card' => $this->card,
            'bank' => $this->bank,
            'donation_foiz' => $this->donation_percent,
            'donation' => $this->donation,
        ];
    }
}
