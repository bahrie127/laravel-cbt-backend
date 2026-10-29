@extends('layouts.app')

@section('title', 'Hasil Ujian')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Hasil Ujian</h1>
            </div>

            <div class="section-body">
                @include('layouts.alert')

                <div class="card">
                    <div class="card-header">
                        <h4>{{ $ujians->total() }} ujian</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('hasil.index') }}" class="form-row mb-3">
                            <div class="col-md-3 col-12 mb-2">
                                <select name="status" class="form-control" aria-label="Filter status" onchange="this.form.submit()">
                                    <option value="">Semua status</option>
                                    <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                                    <option value="berjalan" @selected(request('status') === 'berjalan')>Berjalan</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-12 mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari nama atau email peserta" name="q"
                                        value="{{ request('q') }}" aria-label="Cari peserta">
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
                                        <th>Peserta</th>
                                        <th>Waktu</th>
                                        <th class="text-center">Angka</th>
                                        <th class="text-center">Verbal</th>
                                        <th class="text-center">Logika</th>
                                        <th>Hasil</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ujians as $ujian)
                                        <tr>
                                            <td>
                                                <div class="font-weight-bold">{{ $ujian->user->name }}</div>
                                                <div class="small text-muted">{{ $ujian->user->email }}</div>
                                            </td>
                                            <td class="small">
                                                {{ ($ujian->selesai_at ?? $ujian->created_at)->translatedFormat('d M Y, H.i') }}
                                            </td>
                                            @foreach (['angka', 'verbal', 'logika'] as $f)
                                                <td class="text-center">{{ $ujian->{'status_'.$f} === 'done' ? $ujian->{'nilai_'.$f} : '—' }}</td>
                                            @endforeach
                                            <td>
                                                @if ($ujian->selesai_at)
                                                    <span class="badge {{ $ujian->hasil === 'Lulus' ? 'badge-success' : 'badge-warning' }}">{{ $ujian->hasil }}</span>
                                                @else
                                                    <span class="badge badge-info">Berjalan</span>
                                                @endif
                                            </td>
                                            <td><a href="{{ route('hasil.show', $ujian) }}" class="btn btn-sm btn-light">Detail</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada ujian.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="float-right">{{ $ujians->links() }}</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
