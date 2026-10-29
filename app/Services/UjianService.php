<?php

namespace App\Services;

use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianSoalList;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Alur ujian TPA: satu ujian aktif per peserta (3 kategori), timer dihitung server sejak soal
 * kategori pertama dibuka, jawaban tersimpan (bisa dilanjutkan), nilai dihitung saat kategori
 * diselesaikan atau waktunya habis. Ujian baru hanya dibuat bila ujian sebelumnya sudah selesai.
 */
class UjianService
{
    /** @return list<string> */
    public static function kategori(): array
    {
        return array_keys(config('cbt.kategori'));
    }

    public function aktif(User $user): ?Ujian
    {
        $ujian = $user->ujians()->whereNull('selesai_at')->latest('id')->first();

        if ($ujian) {
            $this->tutupYangHabis($ujian);
        }

        return $ujian?->selesai_at ? null : $ujian;
    }

    public function buatAtauLanjutkan(User $user): Ujian
    {
        return DB::transaction(function () use ($user) {
            // Kunci baris user agar dua klik "mulai" tidak membuat dua ujian.
            User::whereKey($user->id)->lockForUpdate()->first();

            if ($ujian = $this->aktif($user)) {
                return $ujian;
            }

            $data = ['user_id' => $user->id];
            foreach (self::kategori() as $kategori) {
                $data['timer_'.Ujian::field($kategori)] = config('cbt.durasi_menit');
            }
            $ujian = Ujian::create($data);

            foreach (self::kategori() as $kategori) {
                Soal::where('kategori', $kategori)
                    ->inRandomOrder()
                    ->limit(config('cbt.jumlah_soal'))
                    ->pluck('id')
                    ->each(fn ($soalId) => UjianSoalList::create([
                        'ujian_id' => $ujian->id,
                        'soal_id' => $soalId,
                    ]));
            }

            return $ujian->refresh();
        });
    }

    /** Daftar soal kategori (tanpa kunci) + jawaban tersimpan; memulai timer bila belum. */
    public function bukaKategori(Ujian $ujian, string $kategori): array
    {
        if ($ujian->statusOf($kategori) === Ujian::START && ! $ujian->mulaiOf($kategori)) {
            $ujian->update(['mulai_'.Ujian::field($kategori).'_at' => now()]);
        }

        $this->tutupBilaHabis($ujian, $kategori);

        return $this->itemKategori($ujian, $kategori)
            ->map(fn (UjianSoalList $item) => [
                'id' => $item->soal->id,
                'pertanyaan' => $item->soal->pertanyaan,
                'kategori' => $item->soal->kategori,
                'jawaban_a' => $item->soal->jawaban_a,
                'jawaban_b' => $item->soal->jawaban_b,
                'jawaban_c' => $item->soal->jawaban_c,
                'jawaban_d' => $item->soal->jawaban_d,
                'jawaban' => $item->jawaban,
            ])
            ->values()
            ->all();
    }

    public function jawab(Ujian $ujian, int $soalId, string $jawaban): UjianSoalList
    {
        $item = $ujian->soalList()->with('soal')->where('soal_id', $soalId)->first();

        if (! $item) {
            throw ValidationException::withMessages(['soal_id' => 'Soal ini bukan bagian dari ujian Anda.']);
        }

        $kategori = $item->soal->kategori;
        $this->tutupBilaHabis($ujian, $kategori);

        if ($ujian->statusOf($kategori) === Ujian::DONE) {
            throw ValidationException::withMessages(['soal_id' => 'Waktu atau sesi tes ini sudah selesai. Jawaban tidak dapat diubah.']);
        }

        if (! $ujian->mulaiOf($kategori)) {
            throw ValidationException::withMessages(['soal_id' => 'Buka soal tes ini terlebih dahulu.']);
        }

        $item->update([
            'jawaban' => $jawaban,
            'kebenaran' => $item->soal->kunci === $jawaban,
        ]);

        return $item;
    }

    /** Menutup kategori dan menghitung nilai (idempoten). */
    public function selesaikan(Ujian $ujian, string $kategori): array
    {
        $field = Ujian::field($kategori);

        if ($ujian->statusOf($kategori) !== Ujian::DONE) {
            $hasil = $this->hitung($ujian, $kategori);

            $ujian->update([
                'nilai_'.$field => $hasil['nilai'],
                'status_'.$field => Ujian::DONE,
                'mulai_'.$field.'_at' => $ujian->mulaiOf($kategori) ?? now(),
            ]);

            $this->tutupUjianBilaLengkap($ujian);
        }

        return $this->hitung($ujian, $kategori);
    }

    public function hitung(Ujian $ujian, string $kategori): array
    {
        $items = $this->itemKategori($ujian, $kategori);
        $total = $items->count();
        $benar = $items->where('kebenaran', true)->count();
        $kosong = $items->whereNull('jawaban')->count();

        return [
            'kategori' => $kategori,
            'label' => config("cbt.kategori.$kategori.label"),
            'nilai' => $total > 0 ? (int) round($benar / $total * 100) : 0,
            'benar' => $benar,
            'salah' => $total - $benar - $kosong,
            'kosong' => $kosong,
            'jumlah_soal' => $total,
        ];
    }

    public function ringkasan(Ujian $ujian): array
    {
        $this->tutupYangHabis($ujian);

        return [
            'id' => $ujian->id,
            'dibuat_at' => $ujian->created_at?->toIso8601String(),
            'selesai_at' => $ujian->selesai_at?->toIso8601String(),
            'hasil' => $ujian->hasil,
            'rata_rata' => $this->rataRata($ujian),
            'kategori' => collect(self::kategori())->map(function (string $kategori) use ($ujian) {
                $hitung = $this->hitung($ujian, $kategori);
                $selesai = $ujian->statusOf($kategori) === Ujian::DONE;

                // Selama subtes berjalan, nilai & benar/salah disembunyikan agar jawaban tidak bisa ditebak.
                return array_merge($hitung, [
                    'status' => $selesai ? 'selesai' : ($ujian->mulaiOf($kategori) ? 'berjalan' : 'belum'),
                    'terjawab' => $hitung['jumlah_soal'] - $hitung['kosong'],
                    'nilai' => $selesai ? $hitung['nilai'] : null,
                    'benar' => $selesai ? $hitung['benar'] : null,
                    'salah' => $selesai ? $hitung['salah'] : null,
                    'durasi_menit' => $ujian->durasiMenitOf($kategori),
                    'sisa_detik' => $ujian->sisaDetikOf($kategori),
                ]);
            })->values()->all(),
        ];
    }

    public function tutupYangHabis(Ujian $ujian): void
    {
        foreach (self::kategori() as $kategori) {
            $this->tutupBilaHabis($ujian, $kategori);
        }
    }

    public function tutupBilaHabis(Ujian $ujian, string $kategori): void
    {
        $mulai = $ujian->mulaiOf($kategori);

        if ($ujian->statusOf($kategori) === Ujian::DONE || ! $mulai) {
            return;
        }

        $batas = $mulai->copy()->addMinutes($ujian->durasiMenitOf($kategori))->addSeconds(config('cbt.toleransi_detik'));

        if (now()->greaterThan($batas)) {
            $this->selesaikan($ujian, $kategori);
        }
    }

    private function itemKategori(Ujian $ujian, string $kategori)
    {
        return $ujian->soalList()
            ->with('soal')
            ->whereHas('soal', fn ($q) => $q->where('kategori', $kategori))
            ->orderBy('id')
            ->get();
    }

    private function rataRata(Ujian $ujian): ?int
    {
        $nilai = collect(self::kategori())
            ->filter(fn ($k) => $ujian->statusOf($k) === Ujian::DONE)
            ->map(fn ($k) => (int) $ujian->{'nilai_'.Ujian::field($k)});

        return $nilai->isEmpty() ? null : (int) round($nilai->avg());
    }

    private function tutupUjianBilaLengkap(Ujian $ujian): void
    {
        $lengkap = collect(self::kategori())->every(fn ($k) => $ujian->statusOf($k) === Ujian::DONE);

        if ($lengkap && ! $ujian->selesai_at) {
            $rata = $this->rataRata($ujian);
            $ujian->update([
                'selesai_at' => now(),
                'hasil' => $rata >= config('cbt.nilai_lulus') ? 'Lulus' : 'Belum Lulus',
            ]);
        }
    }
}
