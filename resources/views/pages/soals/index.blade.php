@extends('layouts.app')

@section('title', 'Bank Soal')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Bank Soal</h1>
                <div class="section-header-button">
                    <a href="{{ route('soal.create') }}" class="btn btn-primary">Tambah Soal</a>
                </div>
            </div>

            <div class="section-body">
                @include('layouts.alert')
                <p class="section-lead">
                    Setiap ujian mengambil {{ config('cbt.jumlah_soal') }} soal acak per subtes. Kunci jawaban tidak pernah dikirim ke aplikasi peserta.
                </p>

                <div class="card">
                    <div class="card-header">
                        <h4>{{ $soals->total() }} soal</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('soal.index') }}" class="form-row mb-3">
                            <div class="col-md-3 col-12 mb-2">
                                <select name="kategori" class="form-control" aria-label="Filter subtes" onchange="this.form.submit()">
                                    <option value="">Semua subtes</option>
                                    @foreach (config('cbt.kategori') as $kode => $k)
                                        <option value="{{ $kode }}" @selected(request('kategori') === $kode)>{{ $k['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-12 mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari pertanyaan" name="pertanyaan"
                                        value="{{ request('pertanyaan') }}" aria-label="Cari pertanyaan">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" aria-label="Cari"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 110px;">Subtes</th>
                                        <th>Pertanyaan &amp; pilihan</th>
                                        <th class="text-center" style="width: 70px;">Kunci</th>
                                        <th style="width: 200px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($soals as $soal)
                                        <tr>
                                            <td><span class="badge badge-light">{{ config("cbt.kategori.{$soal->kategori}.label", $soal->kategori) }}</span></td>
                                            <td class="py-3">
                                                <div class="font-weight-bold mb-1">{{ $soal->pertanyaan }}</div>
                                                <div class="small text-muted">
                                                    @foreach (['a', 'b', 'c', 'd'] as $opsi)
                                                        <span class="mr-3 {{ $soal->kunci === $opsi ? 'text-success font-weight-bold' : '' }}">{{ strtoupper($opsi) }}. {{ $soal->{'jawaban_'.$opsi} }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="text-center font-weight-bold">{{ strtoupper($soal->kunci) }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('soal.edit', $soal) }}" class="btn btn-sm btn-info text-nowrap"><i class="fas fa-edit"></i> Ubah</a>
                                                    <form action="{{ route('soal.destroy', $soal) }}" method="POST" class="ml-2"
                                                        onsubmit="return confirm('Hapus soal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger text-nowrap"><i class="fas fa-trash"></i> Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada soal yang cocok.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="float-right">{{ $soals->links() }}</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
