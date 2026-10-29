@extends('layouts.app')

@php($judulHalaman = $item->exists ? 'Ubah '.$label : 'Tambah '.$label)

@section('title', $judulHalaman)

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ $kembali }}" class="btn btn-icon" aria-label="Kembali"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $judulHalaman }}</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ $aksi }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if ($item->exists)
                            @method('PUT')
                        @endif
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Judul</label>
                                <input id="title" type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $item->title) }}">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label for="content">Isi</label>
                                <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror"
                                    style="height: 280px;">{{ old('content', $item->content) }}</textarea>
                                <small class="form-text text-muted">Pisahkan paragraf dengan baris kosong. Teks tampil apa adanya di aplikasi.</small>
                                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group mb-0">
                                <label for="image">Gambar (opsional, maks. 2 MB)</label>
                                @if ($item->image_url)
                                    <div class="mb-2 d-flex align-items-center">
                                        <img src="{{ $item->image_url }}" alt="" class="rounded mr-3" style="width: 120px; height: 80px; object-fit: cover;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="hapus_gambar" name="hapus_gambar" value="1">
                                            <label class="custom-control-label" for="hapus_gambar">Hapus gambar</label>
                                        </div>
                                    </div>
                                @endif
                                <input id="image" type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                                <small class="form-text text-muted">Tanpa gambar, aplikasi menampilkan ilustrasi bawaan.</small>
                                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ $kembali }}" class="btn btn-light mr-2">Batal</a>
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
