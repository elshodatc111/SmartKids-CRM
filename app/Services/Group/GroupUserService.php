<?php

namespace App\Services\Group;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\GroupUser;
use Illuminate\Support\Facades\DB;

class GroupUserService{
    public function add($request){
        return DB::transaction(function () use ($request) {
            $activeGroup = GroupUser::where('user_id', $request->user_id)->where('status', 'active')->first();
            if ($activeGroup) {
                return [
                    'message' => 'Hodim allaqachon guruhga biriktirilgan.',
                    'data'    => $activeGroup,
                    'status'  => 409
                ];
            }
            $user = User::findOrFail($request->user_id);
            $type = $user->type;
            if($type != 'tarbiyachi'){
                return [
                    'message' => "Guruhga faqat tarbiyachilarni qo'shish mumkun.",
                    'data'    => $user,
                    'status'  => 409
                ];
            }
            $groupKids = GroupUser::create([
                'group_id'      => $request->group_id,
                'user_id'       => $request->user_id,
                'status'        => 'active',
                'add_data'      => now(),
                'add_admin_id'  => auth()->id(),
            ]);
            return [
                'message' => "Hodim yangi guruhga qo‘shildi.",
                'data'    => $groupKids,
                'status'  => 200
            ];
        });
    }
    public function delete(int $id){
        return DB::transaction(function () use ($id) {
            $groupUser = GroupUser::findOrFail($id);
            if($groupUser->status=='delete'){
                return [
                    'message' => "Hodim allaqachon guruhdan o'chiriligan.",
                    'data'    => $groupUser,
                    'status'  => 409
                ];
            }
            $groupUser->update([
                'status'        => 'delete',
                'delete_data'      => now(),
                'delete_admin_id'  => auth()->id(),
            ]);
            return [
                'message' => "Hodim guruhdan o'chirildi.",
                'data'    => $groupUser,
                'status'  => 200
            ];
        });
    }

}