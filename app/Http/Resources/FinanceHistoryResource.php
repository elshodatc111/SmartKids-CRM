<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceHistoryResource extends JsonResource{
    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'type' => is_string($this->type) ? $this->type : $this->type?->value,
            'reason' => is_string($this->reason) ? $this->reason : $this->reason?->value,
            'amount' => $this->amount,
            'donation' => $this->donation,
            'description' => $this->description,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'admin' => $this->admin ? [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
            ] : null,
            'start_at' => $this->created_at?->format('Y-m-d H:i'),
            'end_at' => $this->updated_at?->format('Y-m-d H:i'),
        ];
    }
}

