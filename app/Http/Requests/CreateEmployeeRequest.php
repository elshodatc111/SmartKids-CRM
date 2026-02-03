<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CreateEmployeeRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'name'          => 'required|string|max:120',
            'phone'         => 'required|string|unique:users,phone',
            'salary_amount' => 'required|numeric',
            'birth'         => 'required|date',
            'series'        => 'nullable|string',
            'type'          => 'required|in:admin,manager,tarbiyachi,oshpaz,hodim',
        ];
    }
}