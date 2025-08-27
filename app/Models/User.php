<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Primary key bukan auto increment
     */
    public $incrementing = false;

    /**
     * Tipe primary key
     * int → kalau manual int
     * string → kalau UUID/ulid
     */
    protected $keyType = 'int';

    /**
     * Primary key field (default 'id')
     */
    protected $primaryKey = 'id';

    /**
     * Field yang boleh diisi massal
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'bio',
        'api_token',
        'photo_url',
    ];

    /**
     * Field yang disembunyikan saat model diubah ke array / JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Casting tipe data otomatis
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Accessor untuk gabungkan first_name dan last_name menjadi full_name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Cek role user (jangan tabrakan dengan spatie hasRole)
     */
    public function checkRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Relasi ke PersonalAccessToken (Sanctum)
     */
    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    /**
     * Relasi ke daftar nilai peserta
     */
    public function daftarNilai(): HasMany
    {
        return $this->hasMany(NilaiPeserta::class, 'user_id');
    }

    /**
     * Relasi ke ujian yang diikuti user
     */
    public function ujians(): BelongsToMany
    {
        return $this->belongsToMany(Ujian::class, 'ujian_users', 'user_id', 'ujian_id')
            ->withPivot('status', 'nilai', 'jawaban')
            ->withTimestamps();
    }

    /**
     * Event boot untuk generate api_token otomatis sebelum simpan user baru
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->api_token)) {
                $user->api_token = Str::random(60);
            }
        });
    }

    /**
     * Override reset password notification
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
