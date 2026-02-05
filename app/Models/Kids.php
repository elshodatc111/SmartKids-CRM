<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Kids extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kids';

    protected $fillable = [
        'full_name',
        'balance',
        'is_active',
        'birth_date',
        'document_series',
        'guardian_name',
        'guardian_phone',
        'photo_path',
        'document_photo_path',
        'guardian_passport_path',
        'health_certificate_path',
        'address',
        'biography',
        'user_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'balance' => 'integer',
        'deleted_at' => 'datetime',
    ];

    // Bu yerda $appends shart emas, chunki Accessor ustun nomi bilan bir xil.
    // Agar $appends ishlatsangiz, nomini boshqacha (masalan: photo_url) qilish kerak edi.
    // Hozirgi holatda buni olib tashlasangiz ham Accessor ishlayveradi.

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * URLni shakllantirish uchun universal yordamchi metod.
     * $attributes['field_name'] orqali bazadagi ASL qiymatni olamiz.
     */
    private function formatUrl(?string $value, string $default): string
    {
        // Agar qiymat null bo'lsa yoki bo'sh bo'lsa default qaytaradi
        if (empty($value) || $value === 'null') {
            return asset('images/' . $default);
        }

        // Agar bazada allaqachon to'liq URL saqlangan bo'lsa (masalan, eski ma'lumotlar)
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Agar rasm storage diskida mavjud bo'lsa Asset URL qaytaradi
        // Aks holda default rasm
        if (Storage::disk('public')->exists($value)) {
            return asset('storage/' . $value);
        }

        return asset('images/' . $default);
    }

    /**
     * ACCESSORS
     * getRawOriginal('field') orqali bazadagi asl matnni tekshiramiz.
     */
    protected function photoPath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->formatUrl($value, 'default-avatar1.png'),
        );
    }

    protected function documentPhotoPath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->formatUrl($value, 'default-document.png'),
        );
    }

    protected function guardianPassportPath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->formatUrl($value, 'default-passport.png'),
        );
    }

    protected function healthCertificatePath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->formatUrl($value, 'default-medical.png'),
        );
    }
}