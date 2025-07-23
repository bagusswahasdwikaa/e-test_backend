<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
