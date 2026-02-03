<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Finance;
class FinanceOutputRequest extends FormRequest{
    public function authorize(): bool{
        return true;
    }
    public function rules(): array{
        return [
            'type'        => 'required|in:cash,card,bank,donation',
            'reason'      => 'required|in:xarajat,daromad,exson',
            'amount'      => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:250',
        ];
    }
    public function withValidator($validator){
        $validator->after(function ($validator) {
            $type   = $this->input('type');
            $reason = $this->input('reason');
            $amount = (float) $this->input('amount');
            if ($reason === 'exson' && $type !== 'donation') {
                $validator->errors()->add(
                    'type',
                    'Exson faqat donation orqali bo‘lishi mumkin'
                );
            }
            if ($type === 'donation' && $reason !== 'exson') {
                $validator->errors()->add(
                    'reason',
                    'Donation faqat exson uchun ishlatiladi'
                );
            }
            $finance = Finance::firstOrCreate(['id' => 1],['cash' => 0,'card' => 0,'bank' => 0,'donation' => 0,]);
            if ($reason === 'xarajat') {
                if (!in_array($type, ['cash', 'card', 'bank', 'donation'])) {
                    $validator->errors()->add('type','Noto‘g‘ri moliya turi');
                    return;
                }
                if ($finance->$type < $amount) {
                    $validator->errors()->add('amount',ucfirst($type) . ' balansida yetarli mablag‘ mavjud emas');
                }
            }
        });
    }
}
