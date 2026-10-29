@extends('layouts.app')

@section('title', 'Konten App')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Konten App</h1>
            </div>

            <div class="section-body">
                @include('layouts.alert')
                <p class="section-lead">Halaman statis yang dibuka dari menu beranda aplikasi peserta.</p>

                <div class="row">
                    @foreach ($contents as $content)
                        <div class="col-md-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ $content->id === 1 ? 'Menu "Tentang Kami"' : ($content->id === 2 ? 'Menu "Tips & Trik"' : 'Konten #'.$content->id) }}</h4>
                                    <div class="card-header-action">
                                        <a href="{{ route('konten.edit', $content) }}" class="btn btn-info"><i class="fas fa-edit"></i> Ubah</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="font-weight-bold mb-2">{{ $content->title }}</div>
                                    <div class="text-muted" style="white-space: pre-line;">{{ \Illuminate\Support\Str::limit($content->content, 320) }}</div>
                                    <div class="small text-muted mt-3">Diperbarui {{ $content->updated_at->translatedFormat('d M Y, H.i') }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
