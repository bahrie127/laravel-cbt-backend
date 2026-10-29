<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\User;
use App\Services\UjianService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $selesai = Ujian::whereNotNull('selesai_at');

        $rataKategori = collect(config('cbt.kategori'))->map(fn ($k) => [
            'label' => $k['label'],
            'rata' => (int) round((clone $selesai)->avg('nilai_'.$k['field']) ?? 0),
        ]);

        return view('pages.dashboard', [
            'jumlahPeserta' => User::where('roles', User::PESERTA)->count(),
            'jumlahSoal' => Soal::count(),
            'soalPerKategori' => Soal::selectRaw('kategori, count(*) as total')->groupBy('kategori')->pluck('total', 'kategori'),
            'ujianSelesai' => (clone $selesai)->count(),
            'ujianBerjalan' => Ujian::whereNull('selesai_at')->count(),
            'tingkatLulus' => (clone $selesai)->count() > 0
                ? (int) round((clone $selesai)->where('hasil', 'Lulus')->count() / (clone $selesai)->count() * 100)
                : 0,
            'rataKategori' => $rataKategori,
            'kategori' => UjianService::kategori(),
            'terbaru' => Ujian::with('user')->whereNotNull('selesai_at')->latest('selesai_at')->limit(8)->get(),
        ]);
    }
}
