<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilUjian extends Model
{
    use HasFactory;

    protected $table = 'hasil_ujian_users';

    protected $fillable = [
        'ujian_user_id',
        'waktu_ujian_selesai',
        'nilai',
        'nama_ujian',
        'status',
    ];

    protected $casts = [
        'waktu_ujian_selesai' => 'datetime',
    ];

    /**
     * Relasi ke ujian_users
     */
    public function ujianUser(): BelongsTo
    {
        return $this->belongsTo(UjianUser::class, 'ujian_user_id');
    }

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id', 'id_ujian');
    }

    public function getNamaUjianAttribute(): ?string
    {
        return $this->ujian?->nama_ujian;
    }
}
