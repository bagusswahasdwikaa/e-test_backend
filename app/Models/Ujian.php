<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_ujian';

    protected $fillable = [
        'nama_ujian',
        'tanggal',
        'durasi',
        'jumlah_soal',
        'nilai',
        'kode_soal',
        'status',
    ];

    public function nilaiPeserta()
    {
        return $this->hasMany(NilaiPeserta::class, 'ujian_id', 'id_ujian');
    }

    public function soals()
    {
        return $this->hasMany(Soal::class, 'ujian_id', 'id_ujian');
    }
}
