@extends('layouts.auth')

@section('title', 'Masuk')

@section('main')
    <div class="card card-primary">
        <div class="card-header">
            <h4>Masuk Panel Pengelola</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" tabindex="1" autocomplete="username" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">Kata sandi</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" tabindex="2" autocomplete="current-password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember">
                        <label class="custom-control-label" for="remember">Ingat saya</label>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Masuk</button>
                </div>
            </form>
        </div>
    </div>
    <div class="text-muted mt-4 text-center small">
        Panel ini khusus admin dan staf. Peserta mengerjakan ujian lewat aplikasi CBT TPA.
    </div>
@endsection
