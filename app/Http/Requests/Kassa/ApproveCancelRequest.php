<?php

namespace App\Http\Requests\Kassa;
use Illuminate\Foundation\Http\FormRequest;
class ApproveCancelRequest extends FormRequest{
    public function authorize(): bool{
        return auth()->user()?->type === 'admin';
    }
    public function rules(): array{
        return [
            'history_id' => ['required','integer','exists:finance_histories,id'],
        ];
    }
}
