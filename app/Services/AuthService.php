<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\Storage;

class AuthService{

    public function login(string $phone, string $password): array{
        $user = User::where('phone', $phone)->first();
        if (!$user || !Hash::check($password, $user->password) || $user->type === 'hodim') {
            throw ValidationException::withMessages([
                'phone' => ['Telefon yoki parol xato'],
            ]);
        }
        return [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => [
                'name'=>$user->name,
                'phone'=>$user->phone,
                'salary_amount'=>$user->salary_amount,
                'birth'=>$user->birth,
                'image'=>$user->image,
                'type'=>$user->type,
            ],
        ];
    }

    public function resetProfile(User $user,string $name,string $birth,string $series): User {
        $user->update([
            'name' => $name,
            'birth' => $birth,
            'series' => $series
        ]);
        return $user;
    }

    public function resetPassword($user, string $currentPassword, string $newPassword): void{
        if (!Hash::check($currentPassword, $user->password)) {
            throw new AccessDeniedHttpException('Eski parol noto‘g‘ri');
        }
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function updateImage($user, $image): string{
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        $path = $image->store('profiles', 'public');
        $user->update([
            'image' => $path,
        ]);
        return $path;
    }

}
