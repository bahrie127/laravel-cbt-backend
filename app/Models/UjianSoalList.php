<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianSoalList extends Model
{
    use HasFactory;

    protected $fillable = [
        'ujian_id',
        'soal_id',
        'jawaban',
        'kebenaran',
    ];

    protected function casts(): array
    {
        return [
            'kebenaran' => 'boolean',
        ];
    }

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
}
