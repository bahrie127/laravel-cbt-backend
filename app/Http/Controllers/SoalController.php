<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSoalRequest;
use App\Models\Soal;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    public function index(Request $request)
    {
        $soals = Soal::query()
            ->when($request->input('pertanyaan'), fn ($q, $cari) => $q->where('pertanyaan', 'like', "%{$cari}%"))
            ->when($request->input('kategori'), fn ($q, $kategori) => $q->where('kategori', $kategori))
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pages.soals.index', compact('soals'));
    }

    public function create()
    {
        return view('pages.soals.form', ['soal' => new Soal]);
    }

    public function store(StoreSoalRequest $request)
    {
        Soal::create($request->validated());

        return redirect()->route('soal.index')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Soal $soal)
    {
        return view('pages.soals.form', compact('soal'));
    }

    public function update(StoreSoalRequest $request, Soal $soal)
    {
        $soal->update($request->validated());

        return redirect()->route('soal.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Soal $soal)
    {
        // Soal yang sudah dipakai ujian tetap disimpan agar nilai peserta tidak berubah.
        if ($soal->ujianSoalLists()->exists()) {
            return back()->with('error', 'Soal sudah dipakai dalam ujian peserta sehingga tidak dapat dihapus. Ubah isinya bila perlu diperbaiki.');
        }

        $soal->delete();

        return redirect()->route('soal.index')->with('success', 'Soal berhasil dihapus.');
    }
}
