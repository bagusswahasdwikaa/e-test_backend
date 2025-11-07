<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UjianUser extends Model
{
    protected $table = 'ujian_users';

    protected $fillable = [
        'ujian_id',
        'user_id',
        'jawaban',
        'hasil_ujian_id',
        'nilai',
        'is_submitted',
        'submitted_at',
        'started_at',   
        'end_time', 
    ];

    protected $casts = [
        'jawaban' => 'array',
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
        'started_at' => 'datetime',   
        'end_time' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke tabel ujian
     */
    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class, 'ujian_id', 'id_ujian');
    }

    /**
     * Relasi ke user yang mengerjakan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Ambil soal-soal dari ujian yang dikerjakan user
     */
    public function soal()
    {
        return $this->ujian ? $this->ujian->soal() : null;
    }

    /**
     * Ambil jawaban user per soal (bentuk array: [soal_id => jawaban])
     */
    public function getJawabanUser()
    {
        return $this->jawaban ?? [];
    }

    /**
     * Koreksi semua soal dan return array hasil koreksi
     */
    public function koreksi()
    {
        $hasil = [];
        $jawabanUser = $this->getJawabanUser();

        // Ambil semua soal dan jawaban dari ujian ini
        $soals = $this->ujian->soals()->with('jawabans')->get();

        foreach ($soals as $soal) {
            $jawabanUserId = $jawabanUser[$soal->id] ?? null;
            $jawabanPeserta = null;
            $jawabanBenar = $soal->jawabans->firstWhere('is_correct', 1);
            $isCorrect = false;

            if ($jawabanUserId) {
                $jawabanPeserta = $soal->jawabans->firstWhere('id', $jawabanUserId);

                // Cek apakah jawaban user adalah jawaban yang benar
                if ($jawabanPeserta && $jawabanPeserta->is_correct) {
                    $isCorrect = true;
                }
            }

            $hasil[] = [
                'soal_id' => $soal->id,
                'pertanyaan' => $soal->pertanyaan,
                'jawaban_user_id' => $jawabanUserId,
                'jawaban_peserta' => $jawabanPeserta?->jawaban,
                'jawaban_benar' => $jawabanBenar?->jawaban,
                'is_correct' => $isCorrect,
            ];
        }

        return $hasil;
    }

    public function hitungNilai(): int
    {
        $hasil = $this->koreksi();
        $jumlahBenar = collect($hasil)->where('is_correct', true)->count();
        $totalSoal = count($hasil);

        return $totalSoal > 0 ? (int) round(($jumlahBenar / $totalSoal) * 100) : 0;
    }


    public function getStatusPesertaAttribute(): string
    {
        return $this->is_submitted ? 'Sudah Dikerjakan' : 'Belum Dikerjakan';
    }

    public function hasilUjian()
    {
        return $this->hasOne(HasilUjian::class, 'ujian_user_id', 'id');
    }
}
