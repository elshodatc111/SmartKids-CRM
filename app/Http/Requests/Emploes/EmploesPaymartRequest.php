<?php

namespace App\Http\Requests\Emploes;

use Illuminate\Foundation\Http\FormRequest;

class EmploesPaymartRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'amount'      => 'required|numeric|min:1', 
            'type'        => 'required|in:cash,card,bank', 
            'description' => 'required|string|max:120',
            'reason'      => 'required|in:ish_haqi' 
        ];
    }
    public function messages(): array{
        return [
            'amount.min' => 'To‘lov miqdori kamida 1 bo‘lishi kerak.',
            'type.in'    => 'To‘lov turi noto‘g‘ri (cash, card yoki bank bo‘lishi shart).',
            'reason.in'  => 'To‘lov turi faqat ish haqi bo‘lishi mumkin.',
        ];
    }
}
