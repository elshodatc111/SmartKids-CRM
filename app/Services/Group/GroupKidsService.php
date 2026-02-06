<?php

namespace App\Services\Group;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\GroupKids;
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
            return [
                'message' => "Bola guruhdan chiqarildi.",
                'data'    => $groupKid->fresh(),
                'status'  => 200
            ];
        });
    }

}