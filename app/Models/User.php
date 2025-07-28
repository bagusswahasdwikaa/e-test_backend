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
    // Traits yang diperlukan
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Field yang dapat diisi secara massal
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * Field yang akan disembunyikan dari array JSON
     */
    protected $hidden = [
        'password',       // Melindungi kata sandi
        'remember_token', // Token ingat pengguna
    ];

    /**
     * Field tipe data yang perlu dirapikan secara otomatis
     */
    protected $casts = [
        'email_verified_at' => 'datetime', 
    ];

    public function hasRole(string $role): bool {
        return $this->role === $role;
    }

    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }
}
