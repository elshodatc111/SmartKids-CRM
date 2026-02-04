<?php

namespace App\Http\Requests\Emploes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmploesUpdateRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        $userId = $this->route('user') ?? $this->route('id');
        return [
            'name'          => 'required|string|max:120',
            'phone'         => [
                'required',
                'string',
                'regex:/^998[0-9]{9}$/', 
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'salary_amount' => 'required|numeric|min:0',
            'birth'         => 'required|date_format:Y-m-d',
            'series'        => 'nullable|string|max:20',
            'type'          => 'required|in:admin,manager,tarbiyachi,oshpaz,hodim',
        ];
    }

    public function messages(): array{
        return [
            'phone.unique' => 'Ushbu telefon raqami boshqa foydalanuvchiga biriktirilgan.',
            'phone.regex'  => 'Telefon raqami 998XXXXXXXXX formatida boʻlishi kerak.',
            'type.in'      => 'Tanlangan lavozim noto‘g‘ri.',
            'birth.date_format' => 'Tug‘ilgan kun Y-m-d (masalan: 1995-05-25) formatida bo‘lishi shart.',
        ];
    }
}
