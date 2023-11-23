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
        'kebenaran',
    ];
}
