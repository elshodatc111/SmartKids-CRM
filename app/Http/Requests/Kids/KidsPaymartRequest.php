<?php

namespace App\Http\Requests\Kids;

use Illuminate\Foundation\Http\FormRequest;

class KidsPaymartRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'payment_type' =>'required|in:cash,card,bank,discount,return_cash,return_card',
            'amount'       => 'required|numeric|min:500',
            'description' => 'nullable|string|max:255',
        ];
    }
    
}
