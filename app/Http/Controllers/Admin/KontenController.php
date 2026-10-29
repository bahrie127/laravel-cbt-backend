<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Konten statis app: id 1 = Tentang Kami, id 2 = Tips & Trik (hanya diubah, tidak ditambah/dihapus). */
class KontenController extends Controller
{
    public function index()
    {
        return view('pages.konten.index', ['contents' => Content::orderBy('id')->get()]);
    }

    public function edit(Content $konten)
    {
        return view('pages.teks-form', [
            'item' => $konten,
            'label' => 'Konten',
            'aksi' => route('konten.update', $konten),
            'kembali' => route('konten.index'),
        ]);
    }

    public function update(Request $request, Content $konten)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:20000',
            'image' => 'nullable|image|max:2048',
            'hapus_gambar' => 'nullable|boolean',
        ], [], ['title' => 'judul', 'content' => 'isi', 'image' => 'gambar']);

        unset($data['hapus_gambar'], $data['image']);

        if ($request->hasFile('image') || $request->boolean('hapus_gambar')) {
            if ($konten->image && ! str_starts_with($konten->image, 'http')) {
                Storage::disk('public')->delete($konten->image);
            }
            $data['image'] = $request->hasFile('image') ? $request->file('image')->store('konten', 'public') : null;
        }

        $konten->update($data);

        return redirect()->route('konten.index')->with('success', 'Konten berhasil diperbarui.');
    }
}
