<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ujian extends Model
{
    use HasFactory;

    /**
     * Nama tabel sesuai migrasi
     */
    protected $table = 'ujians';

    /**
     * Primary key
     */
    protected $primaryKey = 'id_ujian';

    /**
     * Field yang bisa diisi massal
     */
    protected $fillable = [
        'nama_ujian',
        'kode_soal',
        'jumlah_soal',
        'durasi',
        'tanggal_mulai',
        'tanggal_akhir',
        'status',
    ];

    /**
     * Tambahkan atribut status ke JSON response
     */
    protected $appends = ['status'];

    /**
     * Relasi ke nilai peserta
     */
    public function nilaiPeserta(): HasMany
    {
        return $this->hasMany(NilaiPeserta::class, 'ujian_id', 'id_ujian');
    }

    /**
     * Relasi ke soal
     */
    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'ujian_id', 'id_ujian');
    }

    /**
     * Relasi ke user yang mengikuti ujian
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ujian_users', 'ujian_id', 'user_id')
            ->withPivot( 'nilai', 'jawaban')
            ->withTimestamps();
    }

    /**
     * Accessor untuk atribut status
     */
    public function getStatusAttribute(): string
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

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

    public function pivotHasilUjian()
    {
        return $this->hasOne(\App\Models\HasilUjian::class, 'ujian_user_id', 'pivot_id');
    }

}
