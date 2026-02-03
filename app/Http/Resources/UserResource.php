<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource{

    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'salary_amount' => $this->salary_amount,
            'birth' => $this->birth ? Carbon::parse($this->birth)->format('Y-m-d') : null,
            'series' => $this->series,
            'image' => $this->image, // Modeldagi Accessor orqali to'liq URL keladi
            'type' => $this->type,
            'is_active' => (bool)$this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

}
