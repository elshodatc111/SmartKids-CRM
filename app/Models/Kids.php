<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Kids extends Model{
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
    protected $appends = [
        'photo_path',
        'document_photo_path',
        'guardian_passport_path',
        'health_certificate_path',
    ];
    public function creator(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }
    public function scopeActive($query){
        return $query->where('is_active', true);
    }
    private function formatUrl(?string $value, string $default): string{
        if (!$value || in_array($value, ['storage', 'profiles/', 'null'])) {
            return asset('images/' . $default);
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return asset('storage/' . $value);
    }
    protected function photoPath(): Attribute{
        return Attribute::make(
            get: fn ($value) => $this->formatUrl($value, 'default-avatar1.png'),
        );
    }
    protected function documentPhotoPath(): Attribute{
        return Attribute::make(
            get: fn ($value) => $this->formatUrl($value, 'default-document.png'),
        );
    }
    protected function guardianPassportPath(): Attribute{
        return Attribute::make(
            get: fn ($value) => $this->formatUrl($value, 'default-passport.png'),
        );
    }
    protected function healthCertificatePath(): Attribute{
        return Attribute::make(
            get: fn ($value) => $this->formatUrl($value, 'default-medical.png'),
        );
    }

}