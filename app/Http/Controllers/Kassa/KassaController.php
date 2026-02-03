<?php

namespace App\Http\Controllers\Kassa;

use App\Http\Controllers\Controller;
use App\Models\Kassa;
use App\Models\FinanceHistory;
use App\Services\KassaService;
use App\Http\Resources\KassaResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Requests\PendingKassaRequest;
use App\Http\Resources\FinanceHistoryKassaResource;

class KassaController extends Controller{

    public function getKassa(KassaService $kassaService){
        $kassa = $kassaService->getOrCreate();
        return new KassaResource($kassa);
    }

    public function pendingKassa(PendingKassaRequest $request,KassaService $kassaService){
        try {
            $history = $kassaService->createPending(
                $request->validated(),
                auth()->id()
            );
            return response()->json([
                'message' => 'Kassadan chiqim qilindi, tasdiqlanishi kutilmoqda',
                'data'    => new FinanceHistoryKassaResource($history),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }


}
