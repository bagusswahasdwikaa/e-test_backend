<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianUser extends Model
{
    protected $table = 'ujian_users';
    protected $fillable = ['ujian_id', 'user_id', 'jawaban'];

    protected $casts = [
        'jawaban' => 'array'
    ];
}
