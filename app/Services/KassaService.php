<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Kassa;
use App\Models\Finance;
use App\Models\FinanceHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KassaService
{
    private function getLockedKassa(): Kassa
    {
        return Kassa::lockForUpdate()->first() ?? Kassa::create();
    }

    private function getFinance(): Finance
    {
        return Finance::first() ?? Finance::create();
    }

    private function ensureEnoughBalance(Kassa $kassa, string $method, int $amount): void
    {
        if ($kassa->{$method} < $amount) {
            throw new InsufficientBalanceException('Kassada yetarli mablag‘ yo‘q');
        }
    }

    /* ================= KIRIM ================= */

    public function createIncomePending(User $user, array $data): void
    {
        DB::transaction(function () use ($user, $data) {

            $kassa = $this->getLockedKassa();
            $this->ensureEnoughBalance($kassa, $data['payment_method'], $data['amount']);

            $kassa->decrement($data['payment_method'], $data['amount']);
            $kassa->increment('out_'.$data['payment_method'].'_pending', $data['amount']);

            FinanceHistory::create([
                'type' => $data['payment_method'], // cash|card|bank
                'reason' => 'kirim',
                'amount' => $data['amount'],
                'user_id' => $user->id,
                'admin_id' => null,
                'description' => $data['description'] ?? null,
            ]);
        });
    }

    public function approveIncome(FinanceHistory $history, User $admin): void
    {
        DB::transaction(function () use ($history, $admin) {

            $kassa   = $this->getLockedKassa();
            $finance = $this->getFinance();

            $donation = (int) floor($history->amount * $finance->donation_percent / 100);
            $net      = $history->amount - $donation;

            $kassa->decrement('out_'.$history->type.'_pending', $history->amount);
            $finance->increment($history->type, $net);
            $finance->increment('donation', $donation);

            $history->update([
                'admin_id' => $admin->id,
                'donation' => $donation,
            ]);
        });
    }

    public function cancelIncome(FinanceHistory $history): void
    {
        DB::transaction(function () use ($history) {

            $kassa = $this->getLockedKassa();

            $kassa->decrement('out_'.$history->type.'_pending', $history->amount);
            $kassa->increment($history->type, $history->amount);

            $history->delete();
        });
    }

    /* ================= XARAJAT ================= */

    public function createCostPending(User $user, array $data): void
    {
        DB::transaction(function () use ($user, $data) {

            $kassa = $this->getLockedKassa();
            $this->ensureEnoughBalance($kassa, $data['payment_method'], $data['amount']);

            $kassa->decrement($data['payment_method'], $data['amount']);
            $kassa->increment('cost_'.$data['payment_method'].'_pending', $data['amount']);

            FinanceHistory::create([
                'type' => $data['payment_method'], // cash|card|bank
                'reason' => 'xarajat',
                'amount' => $data['amount'],
                'user_id' => $user->id,
                'admin_id' => null,
                'description' => $data['description'] ?? null,
            ]);
        });
    }

    public function approveCost(FinanceHistory $history, User $admin): void
    {
        DB::transaction(function () use ($history, $admin) {

            $kassa   = $this->getLockedKassa();
            $finance = $this->getFinance();

            $donation = (int) floor($history->amount * $finance->donation_percent / 100);

            $kassa->decrement('cost_'.$history->type.'_pending', $history->amount);
            $finance->increment('donation', $donation);

            $history->update([
                'admin_id' => $admin->id,
                'donation' => $donation,
            ]);
        });
    }

    public function cancelCost(FinanceHistory $history): void
    {
        DB::transaction(function () use ($history) {

            $kassa = $this->getLockedKassa();

            $kassa->decrement('cost_'.$history->type.'_pending', $history->amount);
            $kassa->increment($history->type, $history->amount);

            $history->delete();
        });
    }

    /* ================= HOLAT ================= */

    public function getState(): Kassa
    {
        return (Kassa::first() ?? Kassa::create())->fresh();
    }
}
