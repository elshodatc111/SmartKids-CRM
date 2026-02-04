<?php

namespace App\Http\Controllers\Emploes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\Emploes\EmploesUpdateRequest;
use App\Http\Requests\Emploes\EmploesPaymartRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class EmploesController extends Controller{
    protected $userService;
    public function __construct(UserService $userService){
        $this->userService = $userService;
    }
    
    public function allEmploes(){
        $employees = $this->userService->getAllEmployeesExceptAuth();
        return UserResource::collection($employees);
    }

    public function createEmploes(CreateEmployeeRequest $request){
        $user = $this->userService->store($request->validated());
        return response()->json([
            'status'  => true,
            'message' => 'Xodim muvaffaqiyatli qo\'shildi',
            'data'    => new UserResource($user)
        ], 200);
    }

    public function updateEmploes(EmploesUpdateRequest $request, $id){
        $user = $this->userService->updateEmployee($request->validated(), $id);
        return response()->json([
            'status'  => true,
            'message' => 'Xodim ma’lumotlari muvaffaqiyatli yangilandi',
            'data'    => $user
        ], 200);
    }
    public function createPaymart(EmploesPaymartRequest $request, $id){
        try {
            $result = $this->userService->paySalary($request->validated(), $id);            
            return response()->json([
                'status' => true,
                'message' => 'Ish haqi muvaffaqiyatli to’landi',
                'balance' => $result['finance']
            ], 200);            
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400); 
        }
    }

    public function showEmploes($id){

    }
    public function passwordUpdate(Request $request, $id){

    }
    public function createDavomad(Request $request){

    }

}
