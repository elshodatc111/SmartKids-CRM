<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;

class AddKidsGroupRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'group_id' => 'required|integer|exists:groups,id',
            'kids_id'  => 'required|integer|exists:kids,id',
        ];
    }
}
