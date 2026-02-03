<?php

namespace App\Services;

use App\Models\Kassa;
use App\Models\FinanceHistory;
use Illuminate\Support\Facades\DB;

class KassaService{

    public function getOrCreate(): Kassa{
        return Kassa::first() ?? Kassa::create();
    }

    public function createPending(array $data, int $userId): FinanceHistory{
        return DB::transaction(function () use ($data, $userId) {
            $kassa = Kassa::lockForUpdate()->firstOrCreate(['id' => 1]);
            $balanceField = $data['type'];
            $amount = $data['amount'];
            if ($kassa->$balanceField < $amount) {
                throw new \Exception("Kassada yetarli mablag‘ mavjud emas");
            }
            $kassa->$balanceField -= $amount;
            $pendingPrefix = $data['reason'] === 'xarajat' ? 'cost' : 'out';
            $pendingField  = "{$pendingPrefix}_{$balanceField}_pending";
            $kassa->$pendingField += $amount;
            $kassa->save();
            return FinanceHistory::create([
                'type'        => $data['type'],
                'reason'      => $data['reason'],
                'amount'      => $amount,
                'description' => $data['description'] ?? null,
                'user_id'     => $userId,
            ]);
        });
    }

}
