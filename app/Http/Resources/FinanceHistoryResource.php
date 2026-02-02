<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceHistoryResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'reason' => $this->reason->value,
            'amount' => $this->amount,
            'donation' => $this->donation,
            'description' => $this->description,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ],
            'admin' => $this->admin ? [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
            ] : null,
            'start_at' => $this->start_at?->format('Y-m-d H:i'),
            'end_date' => $this->end_date?->format('Y-m-d'),
        ];
    }
}
