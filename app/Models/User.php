<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Primary key bukan auto increment
     */
    public $incrementing = false;

    /**
     * Tipe primary key adalah integer
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
        'id',          // penting untuk isi manual id
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'api_token',
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
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Cek role user secara sederhana
     */
    public function hasRole(string $role): bool
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
    public function daftarNilai()
    {
        return $this->hasMany(NilaiPeserta::class, 'user_id');
    }

    // Tambahkan relasi lain jika ada
}
