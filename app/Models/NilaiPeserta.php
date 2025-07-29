<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPeserta extends Model
{
    use HasFactory;

    protected $table = 'daftar_nilai_peserta';

    protected $fillable = [
        'user_id',
        'ujian_id',
        'tanggal',
        'nilai',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id', 'id_ujian');
    }
}
