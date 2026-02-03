<?php

namespace App\Http\Controllers\Kassa;

use App\Http\Controllers\Controller;
use App\Services\KassaService;
use App\Http\Resources\KassaResource;
use App\Http\Requests\PendingKassaRequest;
use App\Http\Resources\FinanceHistoryKassaResource;
use Illuminate\Support\Facades\DB;

use App\Models\Kassa;
use App\Models\Finance;
use App\Models\FinanceHistory;

class KassaController extends Controller{

    public function getKassa(KassaService $kassaService){
        return new KassaResource($kassaService->getOrCreate());
    }

    public function pendingKassa(PendingKassaRequest $request,KassaService $kassaService) {
        try {
            $history = $kassaService->createPending($request->validated(),auth()->id());
            return response()->json(['message' => 'Kassadan chiqim qilindi, tasdiqlanishi kutilmoqda','data'    => new FinanceHistoryKassaResource($history),], 201);
        } catch (\Exception $e) {return response()->json(['message' => $e->getMessage()], 400);}
    }

    public function successKassa(int $id, KassaService $kassaService){
        try {
            $result = $kassaService->approveTransaction($id,auth()->id());
            return response()->json(['message' => 'So‘rov muvaffaqiyatli tasdiqlandi','donation_applied' => $result['donation_applied'],'donation_amount'  => $result['donation_amount'],], 200);
        } catch (\Exception $e) {return response()->json(['message' => $e->getMessage(),], 400);}
    }

    public function cancelKassaTransaction(int $id, KassaService $kassaService){
        try {
            $kassaService->cancelTransaction($id);
            return response()->json([
                'message' => 'Tranzaksiya muvaffaqiyatli bekor qilindi'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }



}
