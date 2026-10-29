<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Services\UjianService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UjianController extends Controller
{
    public function __construct(private UjianService $ujianService) {}

    /** Ujian aktif peserta (null bila belum ada / sudah selesai semua). */
    public function aktif(Request $request): JsonResponse
    {
        $ujian = $this->ujianService->aktif($request->user());

        return response()->json([
            'message' => $ujian ? 'Ujian aktif ditemukan' : 'Belum ada ujian aktif',
            'data' => $ujian ? $this->ujianService->ringkasan($ujian) : null,
        ]);
    }

    /** Membuat ujian baru, atau mengembalikan ujian yang masih berjalan. */
    public function createUjian(Request $request): JsonResponse
    {
        $ujian = $this->ujianService->buatAtauLanjutkan($request->user());

        return response()->json([
            'message' => $ujian->wasRecentlyCreated ? 'Ujian berhasil dibuat' : 'Melanjutkan ujian yang sedang berjalan',
            'data' => $this->ujianService->ringkasan($ujian),
        ], $ujian->wasRecentlyCreated ? 201 : 200);
    }

    public function getListSoalByKategori(Request $request): JsonResponse
    {
        $kategori = $this->kategori($request);
        $ujian = $this->ujianAtau404($request);

        if ($ujian instanceof JsonResponse) {
            return $ujian;
        }

        $soal = $this->ujianService->bukaKategori($ujian, $kategori);
        $sisa = $ujian->sisaDetikOf($kategori);

        return response()->json([
            'message' => 'Berhasil mendapatkan soal',
            'ujian_id' => $ujian->id,
            'kategori' => $kategori,
            'status' => $ujian->statusOf($kategori),
            'timer' => (int) ceil($sisa / 60),
            'sisa_detik' => $sisa,
            'data' => $soal,
        ]);
    }

    public function jawabSoal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'soal_id' => ['required', 'integer'],
            'jawaban' => ['required', Rule::in(['a', 'b', 'c', 'd'])],
        ]);

        $ujian = $this->ujianAtau404($request);

        if ($ujian instanceof JsonResponse) {
            return $ujian;
        }

        $item = $this->ujianService->jawab($ujian, (int) $data['soal_id'], $data['jawaban']);

        // Kebenaran jawaban sengaja tidak dikirim agar tidak bisa ditebak selama tes.
        return response()->json([
            'message' => 'Berhasil simpan jawaban',
            'data' => [
                'soal_id' => $item->soal_id,
                'jawaban' => $item->jawaban,
                'sisa_detik' => $ujian->sisaDetikOf($item->soal->kategori),
            ],
        ]);
    }

    public function hitungNilaiUjianByKategori(Request $request): JsonResponse
    {
        $kategori = $this->kategori($request);
        $ujian = $this->ujianAtau404($request, includeFinished: true);

        if ($ujian instanceof JsonResponse) {
            return $ujian;
        }

        $hasil = $this->ujianService->selesaikan($ujian, $kategori);

        return response()->json([
            'message' => 'Berhasil mendapatkan nilai',
            'nilai' => $hasil['nilai'],
            'data' => $hasil,
            'ujian' => $this->ujianService->ringkasan($ujian->refresh()),
        ]);
    }

    /** Hasil ujian terakhir + riwayat ujian yang sudah selesai. */
    public function hasil(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->ujianService->aktif($user);

        $terakhir = $user->ujians()->latest('id')->first();
        $riwayat = $user->ujians()->whereNotNull('selesai_at')->latest('selesai_at')->limit(10)->get();

        return response()->json([
            'message' => 'Berhasil mendapatkan hasil ujian',
            'data' => [
                'terakhir' => $terakhir ? $this->ujianService->ringkasan($terakhir) : null,
                'riwayat' => $riwayat->map(fn (Ujian $u) => [
                    'id' => $u->id,
                    'selesai_at' => $u->selesai_at?->toIso8601String(),
                    'hasil' => $u->hasil,
                    'nilai_angka' => $u->nilai_angka,
                    'nilai_verbal' => $u->nilai_verbal,
                    'nilai_logika' => $u->nilai_logika,
                    'rata_rata' => (int) round(($u->nilai_angka + $u->nilai_verbal + $u->nilai_logika) / 3),
                ])->values(),
            ],
        ]);
    }

    private function kategori(Request $request): string
    {
        return $request->validate([
            'kategori' => ['required', Rule::in(UjianService::kategori())],
        ])['kategori'];
    }

    private function ujianAtau404(Request $request, bool $includeFinished = false): Ujian|JsonResponse
    {
        $ujian = $this->ujianService->aktif($request->user());

        // get-nilai boleh dipanggil ulang setelah kategori terakhir ditutup otomatis.
        if (! $ujian && $includeFinished) {
            $ujian = $request->user()->ujians()->latest('id')->first();
        }

        return $ujian ?? response()->json([
            'message' => 'Ujian tidak ditemukan. Mulai ujian terlebih dahulu.',
            'data' => [],
        ], 404);
    }
}
