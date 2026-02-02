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

class FinanceService{

    public function getOrCreateFinance(): Finance{
        return Finance::firstOrCreate([]);
    }

    public function getFinishedHistories(): Collection {
        return FinanceHistory::with([
                'user:id,name',
                'admin:id,name',
            ])
            ->whereNotNull('end_data')
            ->orderBy('id', 'desc')
            ->get();
    }
}