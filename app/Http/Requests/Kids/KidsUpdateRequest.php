<?php

namespace App\Http\Requests\Kids;

use Illuminate\Foundation\Http\FormRequest;

class KidsUpdateRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [            
            'full_name'       => 'required|string|max:255',
            'birth_date'      => 'required|date',
            'guardian_name'   => 'required|string',
            'guardian_phone'  => 'required|string',
            'address'  => 'required|string',
            'biography'  => 'required|string',
        ];
    }
}
