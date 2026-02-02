<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\FinanceRespurce;
use App\Http\Resources\FinanceHistoryResource;
use App\Services\FinanceService;
use App\Models\Finance;
use App\Models\FinanceHistory;


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

    

}
