<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
// 1. Profil ma'lumotlarini olish
    public function show(Request $request) {
        return response()->json($request->user());
    }

    // 2. Ma'lumotlarni yangilash (Ism, tug'ilgan kun va h.k.)
    public function update(Request $request) {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'string|max:255',
            'birth' => 'date',
            'series' => 'string|max:20',
        ]);

        $user->update($data);
        return response()->json(['message' => 'Profil yangilandi', 'user' => $user]);
    }

    // 3. Profil rasmini yuklash
    public function updateImage(Request $request) {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg|max:2048']);
        
        $user = $request->user();

        if ($user->image) {
            Storage::delete($user->image); // Eskisini o'chirish
        }

        $path = $request->file('image')->store('profiles', 'public');
        $user->update(['image' => $path]);

        return response()->json(['message' => 'Rasm yuklandi', 'url' => asset('storage/' . $path)]);
    }

    // 4. Parolni yangilash
    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Eski parol noto\'g\'ri'], 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        return response()->json(['message' => 'Parol muvaffaqiyatli o\'zgartirildi']);
    }
}
