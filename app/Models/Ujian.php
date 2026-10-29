<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Ujian extends Model
{
    use HasFactory;

    public const START = 'start';

    public const DONE = 'done';

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
        'mulai_angka_at',
        'mulai_verbal_at',
        'mulai_logika_at',
        'selesai_at',
    ];

    protected function casts(): array
    {
        return [
            'mulai_angka_at' => 'datetime',
            'mulai_verbal_at' => 'datetime',
            'mulai_logika_at' => 'datetime',
            'selesai_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function soalList(): HasMany
    {
        return $this->hasMany(UjianSoalList::class);
    }

    /** Akhiran kolom untuk kategori (Numeric → angka). */
    public static function field(string $kategori): string
    {
        return config("cbt.kategori.$kategori.field");
    }

    public function statusOf(string $kategori): string
    {
        return $this->{'status_'.self::field($kategori)} ?? self::START;
    }

    public function mulaiOf(string $kategori): ?Carbon
    {
        return $this->{'mulai_'.self::field($kategori).'_at'};
    }

    public function durasiMenitOf(string $kategori): int
    {
        return (int) ($this->{'timer_'.self::field($kategori)} ?: config('cbt.durasi_menit'));
    }

    /** Sisa waktu kategori dalam detik; durasi penuh bila belum dimulai, 0 bila selesai. */
    public function sisaDetikOf(string $kategori): int
    {
        if ($this->statusOf($kategori) === self::DONE) {
            return 0;
        }

        $durasi = $this->durasiMenitOf($kategori) * 60;
        $mulai = $this->mulaiOf($kategori);

        if (! $mulai) {
            return $durasi;
        }

        return max(0, $durasi - (int) $mulai->diffInSeconds(now()));
    }
}
