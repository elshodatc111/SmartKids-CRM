<?php

namespace App\Http\Controllers\Emploes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\CreateEmployeeRequest;
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

    public function showEmploes($id){

    }
    public function updateEmploes(Request $request, $id){

    }
    public function passwordUpdate(Request $request, $id){

    }
    public function createPaymart(Request $request, $id){

    }
    public function createDavomad(Request $request){

    }

}
