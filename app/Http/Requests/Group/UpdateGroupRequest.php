<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'name' => 'required|string|max:120',
            'description' => 'required|string',
            'amount' => 'required|numeric',
        ];
    }
}
