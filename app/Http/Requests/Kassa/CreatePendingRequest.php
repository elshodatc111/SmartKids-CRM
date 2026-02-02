<?php

namespace App\Http\Requests\Kassa;

use Illuminate\Foundation\Http\FormRequest;

class CreatePendingRequest extends FormRequest{
    public function authorize(): bool{
        return auth()->check();
    }
    public function rules(): array{
        return [
            'type' => ['required','in:kirim,xarajat'],
            'payment_method' => ['required','in:cash,card,bank'],
            'amount' => ['required','integer','min:1'],
            'description' => ['nullable','string','max:255'],
        ];
    }
}
