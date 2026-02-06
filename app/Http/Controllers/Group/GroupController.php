<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Group\GroupService;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;

class GroupController extends Controller{

    protected GroupService $groupService;

    public function __construct(GroupService $groupService){
        $this->groupService = $groupService;
    }

    public function all(){
        $result = $this->groupService->all();
        return response()->json([
            'message' => $result['message'],
            'groups'   => $result['groups']
        ], $result['status']);
    }

    public function create(CreateGroupRequest $request){
        $result = $this->groupService->create($request);
        if (isset($result['errors'])) {
            return response()->json($result['errors'], $result['status']);
        }
        return response()->json([
            'message' => $result['message'],
            'group'   => $result['group']
        ], $result['status']);
    }

    public function update(UpdateGroupRequest $request, $id){
        $result = $this->groupService->update($request, $id);
        if (isset($result['errors'])) {
            return response()->json($result['errors'], $result['status']);
        }
        return response()->json([
            'message' => $result['message'],
            'group'   => $result['group']
        ], $result['status']);
    }
    
    public function groupKids($id){
        $result = $this->groupService->groupKids($id);        
        return response()->json([
            'message' => $result['message'],
            'data'   => $result['data']
        ], $result['status']);
    }

    public function show($id){

    }

}
