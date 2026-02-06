<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Group\GroupKidsService;
use App\Http\Requests\Group\AddKidsGroupRequest;

class GroupKidController extends Controller{
    protected GroupKidsService $groupKidsService;

    public function __construct(GroupKidsService $groupKidsService){
        $this->groupKidsService = $groupKidsService;
    }

    public function add(AddKidsGroupRequest $request){
        $result = $this->groupKidsService->add($request);
        return response()->json([
            'message' => $result['message'],
            'data'   => $result['data']
        ], $result['status']);
    }
    public function delete($id){
        $result = $this->groupKidsService->delete($id);
        return response()->json([
            'message' => $result['message'],
            'data'   => $result['data']
        ], $result['status']);
    }
}
