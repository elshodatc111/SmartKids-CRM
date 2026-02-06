<?php

namespace App\Services\Group;

use App\Models\Group;
use App\Models\Kids;
use App\Models\User;
use App\Models\GroupKids;
use App\Models\GroupUser;
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

    public function groupKids(int $groupId){
        Group::findOrFail($groupId);
        $groupKids = GroupKids::with(['group:id,name','kid:id,full_name','addedBy:id,name','deletedBy:id,name',])->where('group_id', $groupId)->orderBy('id', 'desc')->get();
        $data = $groupKids->map(function ($item) {
            return [
                'id' => $item->id,
                'kid' => ['id'   => $item->kids_id,'name' => $item->kid?->full_name,],
                'status' => $item->status,
                'add_data' => $item->add_data,
                'add_admin' => ['id'   => $item->add_admin_id,'name' => $item->addedBy?->name,],
                'delete_data' => $item->delete_data,
                'delete_admin' => $item->delete_admin_id ? ['id'   => $item->delete_admin_id,'name' => $item->deletedBy?->name,] : null,
                'payment_month' => $item->payment_month,
            ];
        });
        return [
            'message' => 'Guruhdagi bolalar tarixi.',
            'data'    => $data,
            'status'  => 200
        ];
    }

    public function groupUsersGet(int $groupId){
        Group::findOrFail($groupId);
        $GroupUser = GroupUser::with(['group:id,name','user:id,name','addedBy:id,name','deletedBy:id,name',])
            ->where('group_id', $groupId)->orderBy('id', 'desc')->get();
        $data = $GroupUser->map(function ($item) {
            return [
                'id' => $item->id,
                'user' => ['id'   => $item->user_id,'name' => $item->user?->name,],
                'status' => $item->status,
                'add_data' => $item->add_data,
                'add_admin' => ['id'   => $item->add_admin_id,'name' => $item->addedBy?->name,],
                'delete_data' => $item->delete_data,
                'delete_admin' => $item->delete_admin_id ? [
                    'id'   => $item->delete_admin_id,
                    'name' => $item->deletedBy?->name
                ] : null,
                'payment_month' => $item->payment_month,
            ];
        });
        return [
            'message' => 'Guruhdagi bolalar tarixi.',
            'data'    => $data,
            'status'  => 200
        ];
    }



}
