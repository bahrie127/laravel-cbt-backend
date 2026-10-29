<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Services\UjianService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HasilUjianController extends Controller
{
    public function index(Request $request): View
    {
        $ujians = Ujian::with('user')
            ->when($request->input('q'), fn ($q, $cari) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%")))
            ->when($request->input('status') === 'selesai', fn ($q) => $q->whereNotNull('selesai_at'))
            ->when($request->input('status') === 'berjalan', fn ($q) => $q->whereNull('selesai_at'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('pages.hasil.index', compact('ujians'));
    }

    public function show(Ujian $ujian, UjianService $service): View
    {
        $ujian->load('user', 'soalList.soal');
        $ringkasan = $service->ringkasan($ujian);
        $soalPerKategori = $ujian->soalList->groupBy(fn ($item) => $item->soal->kategori);

        return view('pages.hasil.show', compact('ujian', 'ringkasan', 'soalPerKategori'));
    }
}
