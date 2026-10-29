@extends('layouts.app')

@section('title', 'Detail Hasil Ujian')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('hasil.index') }}" class="btn btn-icon" aria-label="Kembali"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $ujian->user->name }}</h1>
            </div>

            <div class="section-body">
                <p class="section-lead">
                    Ujian dimulai {{ $ujian->created_at->translatedFormat('d M Y, H.i') }}
                    @if ($ujian->selesai_at)
                        &middot; selesai {{ $ujian->selesai_at->translatedFormat('d M Y, H.i') }}
                        &middot; rata-rata <strong>{{ $ringkasan['rata_rata'] }}</strong>
                        <span class="badge {{ $ujian->hasil === 'Lulus' ? 'badge-success' : 'badge-warning' }}">{{ $ujian->hasil }}</span>
                    @else
                        &middot; <span class="badge badge-info">Berjalan</span>
                    @endif
                </p>

                <div class="row">
                    @foreach ($ringkasan['kategori'] as $k)
                        <div class="col-md-4 col-12">
                            <div class="card">
                                <div class="card-header"><h4>{{ $k['label'] }}</h4></div>
                                <div class="card-body">
                                    <div class="display-4 font-weight-bold mb-2" style="font-size: 2.4rem;">{{ $k['nilai'] ?? '—' }}</div>
                                    <div class="small">
                                        <span class="text-success mr-2">Benar {{ $k['benar'] ?? '—' }}</span>
                                        <span class="text-danger mr-2">Salah {{ $k['salah'] ?? '—' }}</span>
                                        <span class="text-muted">Kosong {{ $k['kosong'] }}</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        {{ ['selesai' => 'Selesai', 'berjalan' => 'Sedang dikerjakan', 'belum' => 'Belum dibuka'][$k['status']] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @foreach ($soalPerKategori as $kategori => $items)
                    <div class="card">
                        <div class="card-header"><h4>Jawaban {{ config("cbt.kategori.$kategori.label") }}</h4></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>Pertanyaan</th>
                                            <th class="text-center" style="width: 90px;">Jawaban</th>
                                            <th class="text-center" style="width: 70px;">Kunci</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $i => $item)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $item->soal->pertanyaan }}</td>
                                                <td class="text-center">
                                                    @if ($item->jawaban)
                                                        <span class="badge {{ $item->kebenaran ? 'badge-success' : 'badge-danger' }}">{{ strtoupper($item->jawaban) }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ strtoupper($item->soal->kunci) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
