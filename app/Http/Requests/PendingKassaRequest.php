<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PendingKassaRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'type'        => 'required|in:cash,card,bank',
            'reason'      => 'required|in:xarajat,kirim',
            'amount'      => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:250',
        ];
    }
}
