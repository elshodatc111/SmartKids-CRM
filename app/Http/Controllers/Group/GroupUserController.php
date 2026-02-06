<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Group\GroupUserService;

class GroupUserController extends Controller{
    protected GroupUserService $groupUserService;

    public function __construct(GroupUserService $groupUserService){
        $this->groupUserService = $groupUserService;
    }

    public function add(Request $request){

    }
    public function delete(Request $request, $id){
        
    }
    
}
