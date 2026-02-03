<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceHistoryKassaResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'reason'      => $this->reason,
            'amount'      => $this->amount,
            'description' => $this->description,
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
