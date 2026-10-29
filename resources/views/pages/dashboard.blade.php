@extends('layouts.app')

@section('title', 'Dashboard')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard</h1>
            </div>

            <div class="row">
                @foreach ([
                    ['Peserta terdaftar', $jumlahPeserta, 'fas fa-user-graduate', 'bg-primary'],
                    ['Butir soal', $jumlahSoal, 'fas fa-list-ol', 'bg-info'],
                    ['Ujian selesai', $ujianSelesai, 'fas fa-clipboard-check', 'bg-success'],
                    ['Ujian berjalan', $ujianBerjalan, 'fas fa-hourglass-half', 'bg-warning'],
                ] as [$label, $nilai, $ikon, $warna])
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon {{ $warna }}"><i class="{{ $ikon }}"></i></div>
                            <div class="card-wrap">
                                <div class="card-header"><h4>{{ $label }}</h4></div>
                                <div class="card-body">{{ number_format($nilai, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-lg-5 col-12">
                    <div class="card">
                        <div class="card-header"><h4>Rata-rata nilai per subtes</h4></div>
                        <div class="card-body">
                            @foreach ($rataKategori as $item)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="font-weight-bold">{{ $item['label'] }}</span>
                                        <span>{{ $item['rata'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar {{ $item['rata'] >= config('cbt.nilai_lulus') ? 'bg-success' : 'bg-warning' }}"
                                            role="progressbar" style="width: {{ $item['rata'] }}%"
                                            aria-valuenow="{{ $item['rata'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @endforeach
                            <p class="text-muted small mb-0">
                                Tingkat kelulusan {{ $tingkatLulus }}% (rata-rata minimal {{ config('cbt.nilai_lulus') }}).
                                Bank soal: @foreach ($kategori as $k)
                                    {{ config("cbt.kategori.$k.label") }} {{ $soalPerKategori[$k] ?? 0 }}@if (! $loop->last), @endif
                                @endforeach butir.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Hasil ujian terbaru</h4>
                            <div class="card-header-action">
                                <a href="{{ route('hasil.index') }}" class="btn btn-primary">Semua hasil</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Peserta</th>
                                            <th class="text-center">Angka</th>
                                            <th class="text-center">Verbal</th>
                                            <th class="text-center">Logika</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($terbaru as $ujian)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('hasil.show', $ujian) }}">{{ $ujian->user->name }}</a>
                                                    <div class="text-muted small">{{ $ujian->selesai_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H.i') }}</div>
                                                </td>
                                                <td class="text-center">{{ $ujian->nilai_angka }}</td>
                                                <td class="text-center">{{ $ujian->nilai_verbal }}</td>
                                                <td class="text-center">{{ $ujian->nilai_logika }}</td>
                                                <td>
                                                    <span class="badge {{ $ujian->hasil === 'Lulus' ? 'badge-success' : 'badge-warning' }}">{{ $ujian->hasil }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada ujian yang selesai.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
