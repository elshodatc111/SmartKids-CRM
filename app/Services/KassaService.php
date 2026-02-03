<?php
namespace App\Services;
use App\Models\Kassa;
use App\Models\FinanceHistory;
use App\Models\Finance;
use Illuminate\Support\Facades\DB;


class KassaService{

    public function getOrCreate(): Kassa{
        return Kassa::firstOrCreate(['id' => 1]);
    }

    public function getSummary(): array    {
        $kassa = $this->getOrCreate();
        $pending = FinanceHistory::whereNull('admin_id')->selectRaw('type, reason, SUM(amount) as total')->groupBy('type', 'reason')->get();
        return [
            'kassa'   => $kassa,
            'pending' => $pending,
        ];
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

    public function approveTransaction(int $id, int $adminId): array{
        return DB::transaction(function () use ($id, $adminId) {
            $history = FinanceHistory::lockForUpdate()->findOrFail($id);
            if ($history->admin_id !== null) {
                throw new \Exception('Bu so‘rov allaqachon tasdiqlangan');
            }
            $kassa   = Kassa::lockForUpdate()->firstOrCreate(['id' => 1]);
            $finance = Finance::lockForUpdate()->firstOrCreate(['id' => 1]);
            $type   = $history->type;     // cash | card | bank
            $reason = $history->reason;   // kirim | xarajat
            $amount = $history->amount;
            $donationPercent   = (float) $finance->donation_percent;
            $expectedDonation  = round($amount * ($donationPercent / 100), 2);
            $donationAmount = $finance->$type >= $expectedDonation ? $expectedDonation : 0;
            $netAmount = $amount - $donationAmount;
            if ($reason === 'xarajat') {
                $pendingField = "cost_{$type}_pending";
                if ($kassa->$pendingField < $amount) {
                    throw new \Exception('Kassadagi pending xarajat yetarli emas');
                }
                $kassa->$pendingField -= $amount;
                if ($donationAmount > 0) {
                    $finance->$type     -= $donationAmount;
                    $finance->donation += $donationAmount;
                }
            }elseif ($reason === 'kirim') {
                $pendingField = "out_{$type}_pending";
                if ($kassa->$pendingField < $amount) {
                    throw new \Exception('Kassadagi pending kirim yetarli emas');
                }
                $kassa->$pendingField -= $amount;
                $finance->$type      += $netAmount;
                if ($donationAmount > 0) {
                    $finance->donation += $donationAmount;
                }
            }else {
                throw new \Exception('Noto‘g‘ri reason turi');
            }
            $kassa->save();
            $finance->save();
            $history->update([
                'admin_id' => $adminId,
                'end_data' => now(),
                'donation' => $donationAmount,
                'status'   => 'approved',
            ]);
            return [
                'donation_applied' => $donationAmount > 0,
                'donation_amount'  => $donationAmount,
            ];
        });
    }

    public function cancelTransaction(int $id): void{
        DB::transaction(function () use ($id) {
            $history = FinanceHistory::lockForUpdate()->find($id);
            if (!$history) {
                throw new \Exception('Tranzaksiya topilmadi');
            }
            if ($history->admin_id !== null) {
                throw new \Exception('Tasdiqlangan tranzaksiyani bekor qilib bo‘lmaydi');
            }
            $kassa = Kassa::lockForUpdate()->firstOrCreate(['id' => 1]);
            $type   = $history->type;     // cash | card | bank
            $reason = $history->reason;   // kirim | xarajat
            $amount = $history->amount;
            if ($reason === 'xarajat') {
                $pendingField = "cost_{$type}_pending";
                if ($kassa->$pendingField < $amount) {throw new \Exception('Kassadagi pending xarajat noto‘g‘ri holatda');}
                $kassa->$pendingField -= $amount;
                $kassa->$type += $amount;
            }
            elseif ($reason === 'kirim') {
                $pendingField = "out_{$type}_pending";
                if ($kassa->$pendingField < $amount) {
                    throw new \Exception('Kassadagi pending kirim noto‘g‘ri holatda');
                }
                $kassa->$pendingField -= $amount;
                $kassa->$type += $amount;
            }
            else {throw new \Exception('Noto‘g‘ri reason turi');}
            $kassa->save();
            $history->delete();
        });
    }

}
