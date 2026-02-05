<?php

namespace App\Services\Kids;

use App\Models\Kids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class KidsService{
    public function all(){
        return Kids::with('creator')->latest()->get();
    }

    public function active(){
        return Kids::where('is_active', true)->with('creator')->latest()->get();
    }

    public function inactive(){
        return Kids::where('is_active', false)->with('creator')->latest()->get();
    }

    public function toggleActive(Request $request){
        $request->validate(['id' => 'required|exists:kids,id']);
        $kid = Kids::findOrFail($request->id);
        $kid->update(['is_active' => !$kid->is_active]);
        return [
            'message'   => 'Status o\'zgardi',
            'is_active' => $kid->is_active
        ];
    }
    public function create(Request $request){
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $kid = Kids::create($data);
        return [
            'message' => 'Bola yaratildi',
            'kid'     => $kid,
            'status'  => 200
        ];
    }

    public function update(Request $request, int $id): array{
        $kid = Kids::findOrFail($id);
        $data = method_exists($request, 'validated')? $request->validated(): $request->all();
        $kid->update($data);
        return [
            'message' => 'Bola maʼlumotlari yangilandi',
            'kid'     => $kid->fresh(),
            'status'  => 200
        ];
    }

}
