<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Group\GroupUserService;  // 
use App\Http\Requests\Group\GroupCreateUserRequest;

class GroupUserController extends Controller{
    protected GroupUserService $groupUserService;

    public function __construct(GroupUserService $groupUserService){
        $this->groupUserService = $groupUserService;
    }

    public function add(GroupCreateUserRequest $request){
        $result = $this->groupUserService->add($request);
        return response()->json([
            'message' => $result['message'],
            'data'   => $result['data']
        ], $result['status']);
    }
    public function delete(Request $request, $id){
        
    }
    
}
