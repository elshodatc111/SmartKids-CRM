<?php
namespace App\Services;

use App\Models\User;
use App\Models\Finance;
use App\Models\FinanceHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FinanceService{

    public function getOrCreateFinance(): Finance{
        return Finance::firstOrCreate([]);
    }

    public function getFinishedHistories(): Collection{
        return FinanceHistory::with(['user:id,name','admin:id,name',])->whereNotNull('admin_id')->orderByDesc('id')->get();
    }

    public function financeOutput(array $data, int $userId): void{
        DB::transaction(function () use ($data, $userId) {
            $type   = $data['type'];     // cash|card|bank|donation
            $reason = $data['reason'];   // xarajat|daromad|exson
            $amount = (float) $data['amount'];
            $finance = Finance::lockForUpdate()->firstOrCreate(['id' => 1],['cash'     => 0,'card'     => 0,'bank'     => 0,'donation' => 0,]);
            if ($reason === 'xarajat') {
                if ($finance->$type < $amount) {
                    throw new \Exception(ucfirst($type) . ' balansida yetarli mablag‘ mavjud emas');
                }
                $finance->$type -= $amount;
            }elseif ($reason === 'daromad') {
                $finance->$type -= $amount;
            }elseif ($reason === 'exson') {
                $finance->donation -= $amount;
            }else {throw new \Exception('Noto‘g‘ri reason turi');}
            $finance->save();
            FinanceHistory::create([
                'type'        => $type,
                'reason'      => $reason,
                'amount'      => $amount,
                'description' => $data['description'] ?? null,
                'user_id'     => $userId,
                'admin_id'    => $userId,
                'start_at'    => now(),
                'end_data'    => now(),
                'status'      => 'approved',
            ]);
        });
    }
}