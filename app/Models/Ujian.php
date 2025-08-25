<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ujian extends Model
{
    use HasFactory;

    // Pakai nama tabel sesuai migrasi
    protected $table = 'ujians';

    protected $primaryKey = 'id_ujian';

    protected $fillable = [
        'nama_ujian',
        'kode_soal',
        'jumlah_soal',
        'durasi',
        'tanggal_mulai',
        'tanggal_akhir',
        'status',
        'nilai',
    ];

    // Relasi ke NilaiPeserta
    public function nilaiPeserta()
    {
        return $this->hasMany(NilaiPeserta::class, 'ujian_id', 'id_ujian');
    }

    // Relasi ke Soal
    public function soals()
    {
        return $this->hasMany(Soal::class, 'ujian_id', 'id_ujian');
    }

    // Supaya field "status" otomatis ikut di JSON
    protected $appends = ['status'];

    /**
     * Accessor untuk atribut status
     */
    public function getStatusAttribute(): string
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

        // Jika salah satu tanggal kosong, default Non Aktif
        if (!$this->tanggal_mulai || !$this->tanggal_akhir) {
            return 'Non Aktif';
        }

        $now = Carbon::now($tz);
        $mulai = Carbon::parse($this->tanggal_mulai, $tz);
        $akhir = Carbon::parse($this->tanggal_akhir, $tz);

        if ($now->lt($mulai)) {
            return 'Belum Dimulai';
        } elseif ($now->between($mulai, $akhir)) {
            return 'Aktif';
        } else {
            return 'Selesai';
        }
    }
}
