<?php

namespace App\Http\Controllers\Kassa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kassa\CreatePendingRequest;
use App\Http\Requests\Kassa\ApproveCancelRequest;
use App\Models\FinanceHistory;
use App\Services\KassaService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\KassaStateResource;

class KassaController extends Controller
{
    public function __construct(private readonly KassaService $service) {}

    public function state(): JsonResponse
    {
        return response()->json([
            'data' => new KassaStateResource($this->service->getState())
        ]);
    }

    public function createPending(CreatePendingRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($data['type'] === 'kirim') {
            $this->service->createIncomePending($request->user(), $data);
        } else {
            $this->service->createCostPending($request->user(), $data);
        }

        return response()->json(['message' => 'Pending yaratildi'], 201);
    }

    public function approve(ApproveCancelRequest $request): JsonResponse
    {
        $history = FinanceHistory::whereNull('admin_id')
            ->findOrFail($request->validated()['history_id']);

        $history->type === 'kirim'
            ? $this->service->approveIncome($history, $request->user())
            : $this->service->approveCost($history, $request->user());

        return response()->json(['message' => 'Tasdiqlandi']);
    }

    public function cancel(ApproveCancelRequest $request): JsonResponse
    {
        $history = FinanceHistory::whereNull('admin_id')
            ->findOrFail($request->validated()['history_id']);

        $history->type === 'kirim'
            ? $this->service->cancelIncome($history)
            : $this->service->cancelCost($history);

        return response()->json(['message' => 'Bekor qilindi']);
    }
}
