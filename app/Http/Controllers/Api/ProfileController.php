<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\AuthService;
use App\Http\Requests\Auth\{LoginRequest,ResetPasswordRequest,UpdateImagesRequest,UpdateProfileRequest};
use App\Http\Resources\UserResource;

class ProfileController extends Controller{
    public function __construct(
        private AuthService $authService
    ) {}

    public function show(Request $request) {
        return new UserResource($request->user());
    }

    public function update(UpdateProfileRequest $request) {
        $data = $this->authService->resetProfile(
            $request->user(),
            $request->name,
            $request->birth,
            $request->series,
        );
        return response()->json([
            'message' => 'Maʼlumotlar yangilandi',
            'user' => [
                'name' => $data->name,
                'birth' => $data->birth,
                'series' => $data->series,
            ],
        ]);
    }

    public function updateImage(UpdateImagesRequest $request) {        
        $path = $this->authService->updateImage(
            $request->user(),
            $request->file('image')
        );
        return response()->json([
            'message' => 'Rasm yuklandi',
            'url' => asset('storage/' . $path),
        ]);
    }

    public function updatePassword(ResetPasswordRequest $request) {
        $this->authService->resetPassword(
            $request->user(),
            $request->current_password,
            $request->new_password
        );
        return response()->json([
            'message' => 'Parol muvaffaqiyatli o‘zgartirildi',
        ]);
    }
}
