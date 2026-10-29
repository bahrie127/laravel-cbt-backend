@extends('layouts.app')

@section('title', $user->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('user.index') }}" class="btn btn-icon" aria-label="Kembali"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $user->exists ? 'Ubah Pengguna' : 'Tambah Pengguna' }}</h1>
            </div>

            <div class="section-body">
                @include('layouts.alert')
                <div class="card">
                    <form action="{{ $user->exists ? route('user.update', $user) : route('user.store') }}" method="POST">
                        @csrf
                        @if ($user->exists)
                            @method('PUT')
                        @endif
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6 col-12">
                                    <label for="name">Nama</label>
                                    <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label for="phone">No. HP (opsional)</label>
                                    <input id="phone" type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $user->phone) }}">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label for="password">Kata sandi{{ $user->exists ? ' baru (kosongkan bila tidak diganti)' : '' }}</label>
                                    <input id="password" type="password" name="password" autocomplete="new-password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="d-block">Peran</label>
                                <div class="selectgroup w-100">
                                    @foreach (['ADMIN' => 'Admin', 'STAFF' => 'Staf', 'USER' => 'Peserta'] as $kode => $label)
                                        <label class="selectgroup-item">
                                            <input type="radio" name="roles" value="{{ $kode }}" class="selectgroup-input"
                                                @checked(old('roles', $user->roles) === $kode)>
                                            <span class="selectgroup-button">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('roles')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('user.index') }}" class="btn btn-light mr-2">Batal</a>
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
