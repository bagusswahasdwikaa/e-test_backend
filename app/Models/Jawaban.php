<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasFactory;

    protected $fillable = [
        'soal_id', 'jawaban', 'is_correct'
    ];

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
}
