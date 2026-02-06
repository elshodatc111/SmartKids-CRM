<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'name' => 'required|string|max:120|unique:groups,name',
            'description' => 'required|string',
            'amount' => 'required|numeric',
        ];
    }
}
