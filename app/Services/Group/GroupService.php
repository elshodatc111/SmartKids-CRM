<?php

namespace App\Services\Group;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupService{
    public function all(){
        $groups = Group::with('user')->get()->map(function ($group) {
                return [
                    'id'          => $group->id,
                    'name'        => $group->name,
                    'description' => $group->description,
                    'amount'      => $group->amount,
                    'user_id'     => $group->user_id,
                    'user_name'   => $group->user->name ?? null,
                    'created_at'  => $group->created_at,
                    'updated_at'  => $group->updated_at,
                ];
            });
        return [
            'message' => 'Barcha guruhlar.',
            'groups'  => $groups,
            'status'  => 200
        ];
    }

    public function create(Request $request){
        $group = Group::create([
            'name'        => $request->name,
            'description' => $request->description,
            'amount'      => $request->amount,
            'user_id'     => auth()->id(),
        ]);
        return [
            'message' => 'Yangi guruh ochildi.',
            'group'   => $group,
            'status'  => 200
        ];
    }

    public function update(Request $request,int $id){
        $group = Group::findOrFail($id);
        $group->update([
            'name'        => $request->name,
            'description' => $request->description,
            'amount'      => $request->amount,
        ]);
        return [
            'message' => 'Guruh malumoti yangilandi.',
            'group'   => $group,
            'status'  => 200
        ];
    }



}
