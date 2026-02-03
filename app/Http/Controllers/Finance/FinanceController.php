<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\FinanceRespurce;
use App\Http\Resources\FinanceHistoryResource;
use App\Services\FinanceService;
use App\Models\Finance;
use App\Models\FinanceHistory;
use App\Http\Requests\Finance\FinanceOutputRequest;
use App\Http\Requests\Finance\UpdateDonationPercentRequest;
use Illuminate\Support\Facades\DB;


class FinanceController extends Controller{

    public function __construct(private FinanceService $financeService) {}

    public function getFinance(){
        $finance = $this->financeService->getOrCreateFinance();
        return new FinanceRespurce($finance);
    }

    public function getFinanceHistory(){
        $histories = $this->financeService->getFinishedHistories();
        return FinanceHistoryResource::collection($histories);
    }

    public function getFinanceOutput(FinanceOutputRequest $request,FinanceService $financeService) {
        try {
            $financeService->financeOutput($request->validated(),auth()->id());
            return response()->json(['message' => 'Finance tranzaksiya muvaffaqiyatli amalga oshirildi'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateDonationPercent(UpdateDonationPercentRequest $request,FinanceService $financeService) {
        $finance = $financeService->updateDonationPercent(
            $request->donation_percent
        );
        return response()->json([
            'message' => 'Donation percent muvaffaqiyatli yangilandi',
            'donation_percent' => $finance->donation_percent,
        ], 200);
    }

}
