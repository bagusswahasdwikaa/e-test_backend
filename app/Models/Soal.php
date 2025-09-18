<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $table = 'soals';
    protected $primaryKey = 'id'; // penting
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ujian_id',
        'pertanyaan',
        'media_path',
        'media_type',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id', 'id_ujian');
    }

    public function jawabans()
    {
        return $this->hasMany(Jawaban::class, 'soal_id', 'id');
    }

    public function getMediaUrlAttribute()
    {
        return $this->media_path ? asset('storage/' . $this->media_path) : null;
    }
}