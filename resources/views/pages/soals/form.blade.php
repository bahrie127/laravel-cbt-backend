@extends('layouts.app')

@section('title', $soal->exists ? 'Ubah Soal' : 'Tambah Soal')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('soal.index') }}" class="btn btn-icon" aria-label="Kembali"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $soal->exists ? 'Ubah Soal' : 'Tambah Soal' }}</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <form action="{{ $soal->exists ? route('soal.update', $soal) : route('soal.store') }}" method="POST">
                        @csrf
                        @if ($soal->exists)
                            @method('PUT')
                        @endif
                        <div class="card-body">
                            <div class="form-group">
                                <label for="kategori">Subtes</label>
                                <select id="kategori" name="kategori" class="form-control @error('kategori') is-invalid @enderror">
                                    @foreach (config('cbt.kategori') as $kode => $k)
                                        <option value="{{ $kode }}" @selected(old('kategori', $soal->kategori) === $kode)>{{ $k['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label for="pertanyaan">Pertanyaan</label>
                                <textarea id="pertanyaan" name="pertanyaan" class="form-control @error('pertanyaan') is-invalid @enderror"
                                    style="height: 110px;">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                                @error('pertanyaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="row">
                                @foreach (['a', 'b', 'c', 'd'] as $opsi)
                                    <div class="form-group col-md-6 col-12">
                                        <label for="jawaban_{{ $opsi }}">Pilihan {{ strtoupper($opsi) }}</label>
                                        <input id="jawaban_{{ $opsi }}" type="text" name="jawaban_{{ $opsi }}"
                                            class="form-control @error('jawaban_'.$opsi) is-invalid @enderror"
                                            value="{{ old('jawaban_'.$opsi, $soal->{'jawaban_'.$opsi}) }}">
                                        @error('jawaban_'.$opsi)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-group mb-0">
                                <label class="d-block">Kunci jawaban</label>
                                <div class="selectgroup w-100">
                                    @foreach (['a', 'b', 'c', 'd'] as $opsi)
                                        <label class="selectgroup-item">
                                            <input type="radio" name="kunci" value="{{ $opsi }}" class="selectgroup-input"
                                                @checked(old('kunci', $soal->kunci) === $opsi)>
                                            <span class="selectgroup-button">{{ strtoupper($opsi) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('kunci')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('soal.index') }}" class="btn btn-light mr-2">Batal</a>
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
