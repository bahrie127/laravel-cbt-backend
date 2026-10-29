@extends('layouts.app')

@section('title', 'Materi')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Materi TPA</h1>
                <div class="section-header-button">
                    <a href="{{ route('materi.create') }}" class="btn btn-primary">Tambah Materi</a>
                </div>
            </div>

            <div class="section-body">
                @include('layouts.alert')
                <p class="section-lead">Materi tampil di menu Materi aplikasi peserta sesuai urutan di bawah.</p>

                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('materi.index') }}" class="mb-3" style="max-width: 420px;">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari judul" name="q" value="{{ request('q') }}" aria-label="Cari judul">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" aria-label="Cari"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Judul &amp; ringkasan</th>
                                        <th style="width: 90px;">Gambar</th>
                                        <th style="width: 200px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($materis as $materi)
                                        <tr>
                                            <td class="py-3">
                                                <div class="font-weight-bold">{{ $materi->title }}</div>
                                                <div class="small text-muted">{{ \Illuminate\Support\Str::limit($materi->content, 140) }}</div>
                                            </td>
                                            <td>
                                                @if ($materi->image_url)
                                                    <img src="{{ $materi->image_url }}" alt="" class="rounded" style="width: 64px; height: 48px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('materi.edit', $materi) }}" class="btn btn-sm btn-info text-nowrap"><i class="fas fa-edit"></i> Ubah</a>
                                                    <form action="{{ route('materi.destroy', $materi) }}" method="POST" class="ml-2"
                                                        onsubmit="return confirm('Hapus materi ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger text-nowrap"><i class="fas fa-trash"></i> Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada materi.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="float-right">{{ $materis->links() }}</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
