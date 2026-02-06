<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class GroupCreateUserRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'group_id' => 'required|integer|exists:groups,id',
            'user_id'  => 'required|integer|exists:users,id',
        ];
    }
}
