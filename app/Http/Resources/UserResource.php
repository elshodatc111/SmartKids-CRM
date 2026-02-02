<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource{

    public function toArray(Request $request): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'birth' => $this->birth,
            'series' => $this->series,
            'salary_amount' => $this->salary_amount,
            'type' => $this->type,
            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }

}
