<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDonationPercentRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return ['donation_percent' => 'required|numeric|min:0|max:100'];
    }
}
