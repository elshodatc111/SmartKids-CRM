<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kassa extends Model{
    use HasFactory;
    protected $table = 'kassas';

    protected $fillable = [
        'cash',
        'card',
        'bank',
        'out_cash_pending',
        'out_card_pending',
        'out_bank_pending',
        'cost_cash_pending',
        'cost_card_pending',
        'cost_bank_pending',
    ];

    protected $casts = [
        'cash' => 'integer',
        'card' => 'integer',
        'bank' => 'integer',
        'out_cash_pending' => 'integer',
        'out_card_pending' => 'integer',
        'out_bank_pending' => 'integer',
        'cost_cash_pending' => 'integer',
        'cost_card_pending' => 'integer',
        'cost_bank_pending' => 'integer',
    ];
    public function getTotalBalanceAttribute(): int{
        return $this->cash + $this->card + $this->bank;
    }
    public function getTotalOutPendingAttribute(): int{
        return $this->out_cash_pending
            + $this->out_card_pending
            + $this->out_bank_pending;
    }
    public function getTotalCostPendingAttribute(): int{
        return $this->cost_cash_pending
            + $this->cost_card_pending
            + $this->cost_bank_pending;
    }
}
