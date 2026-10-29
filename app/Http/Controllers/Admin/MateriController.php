<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index(Request $request)
    {
        $materis = Materi::query()
            ->when($request->input('q'), fn ($q, $cari) => $q->where('title', 'like', "%{$cari}%"))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.materi.index', compact('materis'));
    }

    public function create()
    {
        return $this->form(new Materi, route('materi.store'));
    }

    public function store(Request $request)
    {
        Materi::create($this->data($request));

        return redirect()->route('materi.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Materi $materi)
    {
        return $this->form($materi, route('materi.update', $materi));
    }

    private function form(Materi $materi, string $aksi)
    {
        return view('pages.teks-form', [
            'item' => $materi,
            'label' => 'Materi',
            'aksi' => $aksi,
            'kembali' => route('materi.index'),
        ]);
    }

    public function update(Request $request, Materi $materi)
    {
        $materi->update($this->data($request, $materi));

        return redirect()->route('materi.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Materi $materi)
    {
        $this->hapusGambar($materi->image);
        $materi->delete();

        return redirect()->route('materi.index')->with('success', 'Materi berhasil dihapus.');
    }

    private function data(Request $request, ?Materi $materi = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:20000',
            'image' => 'nullable|image|max:2048',
            'hapus_gambar' => 'nullable|boolean',
        ], [], ['title' => 'judul', 'content' => 'isi materi', 'image' => 'gambar']);

        unset($data['hapus_gambar']);

        if ($request->hasFile('image')) {
            $this->hapusGambar($materi?->image);
            $data['image'] = $request->file('image')->store('materi', 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            $this->hapusGambar($materi?->image);
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        return $data;
    }

    private function hapusGambar(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
