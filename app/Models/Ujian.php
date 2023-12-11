<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nilai_angka',
        'nilai_verbal',
        'nilai_logika',
        'hasil',
        'status_angka',
        'status_verbal',
        'status_logika',
        'timer_angka',
        'timer_verbal',
        'timer_logika',

    ];
}
