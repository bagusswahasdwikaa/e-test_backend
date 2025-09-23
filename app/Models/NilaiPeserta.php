<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiPeserta extends Model
{
    protected $table = 'nilai_pesertas';

    protected $fillable = [
        'user_id',
        'ujian_id',
        'nilai',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class, 'ujian_id', 'id_ujian');
    }
}
