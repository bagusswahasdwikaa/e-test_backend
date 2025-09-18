<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $primaryKey = 'id'; // atau 'id'
    public $incrementing = true;
    protected $keyType = 'int';
 
    use HasFactory;

    protected $fillable = [
        'soal_id',
        'jawaban',
        'is_correct'
    ];

    public function soal()
    {
        // tambahkan key mapping jelas
        return $this->belongsTo(Soal::class, 'soal_id', 'id_soal');
    }
}
