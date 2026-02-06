<?php

namespace App\Services\Group;

use App\Models\Group;
use App\Models\Kids;
use Illuminate\Http\Request;
use App\Models\GroupKids;
use App\Models\KidsHistory;
use Illuminate\Support\Facades\DB;

class GroupKidsService{
    
    public function add($request){
        return DB::transaction(function () use ($request) {
            $activeGroup = GroupKids::where('kids_id', $request->kids_id)->where('status', 'active')->first();
            if ($activeGroup) {
                return [
                    'message' => 'Bola allaqachon guruhga biriktirilgan.',
                    'data'    => $activeGroup,
                    'status'  => 409
                ];
            }
            $groupKids = GroupKids::create([
                'group_id'      => $request->group_id,
                'kids_id'       => $request->kids_id,
                'status'        => 'active',
                'add_data'      => now(),
                'add_admin_id'  => auth()->id(),
            ]);
            KidsHistory::create([
                'kids_id' => $request->kids_id,
                'type' => 'group_add',
                'group_id' => $request->group_id,
                'description' => Group::find($request->group_id)->name." guruhga qo'shildi.",
                'user_id' => auth()->id()
            ]);
            $Kids = Kids::find($request->kids_id);
            $Kids->is_active = true;
            $Kids->save();
            return [
                'message' => "Bola yangi guruhga qo‘shildi.",
                'data'    => $groupKids,
                'status'  => 200
            ];
        });
    }

    public function delete(int $id){
        return DB::transaction(function () use ($id) {
            $groupKid = GroupKids::findOrFail($id);
            if ($groupKid->status === 'delete') {
                return [
                    'message' => "Bu tranzaksiya oldin o‘chirilgan.",
                    'data'    => [],
                    'status'  => 409
                ];
            }
            $groupKid->update([
                'status'           => 'delete',
                'delete_data'       => now(),
                'delete_admin_id'  => auth()->id(),
            ]);
            KidsHistory::create([
                'kids_id' => $groupKid->kids_id,
                'type' => 'group_delte',
                'group_id' => $groupKid->group_id,
                'description' => Group::find($groupKid->group_id)->name." guruhga o'chirildi.",
                'user_id' => auth()->id()
            ]);
            $Kids = Kids::find($groupKid->kids_id);
            $Kids->is_active = false;
            $Kids->save();
            return [
                'message' => "Bola guruhdan chiqarildi.",
                'data'    => $groupKid->fresh(),
                'status'  => 200
            ];
        });
    }

}