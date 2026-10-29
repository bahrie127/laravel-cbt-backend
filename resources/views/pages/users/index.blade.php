@extends('layouts.app')

@section('title', 'Pengguna')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Pengguna</h1>
                <div class="section-header-button">
                    <a href="{{ route('user.create') }}" class="btn btn-primary">Tambah Pengguna</a>
                </div>
            </div>

            <div class="section-body">
                @include('layouts.alert')
                <p class="section-lead">Admin dan staf masuk ke panel ini; peserta hanya bisa masuk ke aplikasi ujian.</p>

                <div class="card">
                    <div class="card-header">
                        <h4>{{ $users->total() }} pengguna</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('user.index') }}" class="form-row mb-3">
                            <div class="col-md-3 col-12 mb-2">
                                <select name="roles" class="form-control" aria-label="Filter peran" onchange="this.form.submit()">
                                    <option value="">Semua peran</option>
                                    @foreach (['ADMIN' => 'Admin', 'STAFF' => 'Staf', 'USER' => 'Peserta'] as $kode => $label)
                                        <option value="{{ $kode }}" @selected(request('roles') === $kode)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-12 mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari nama atau email" name="name"
                                        value="{{ request('name') }}" aria-label="Cari pengguna">
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
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. HP</th>
                                        <th>Peran</th>
                                        <th class="text-center">Ujian selesai</th>
                                        <th style="width: 200px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td class="font-weight-bold">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone ?: '—' }}</td>
                                            <td>
                                                <span class="badge {{ ['ADMIN' => 'badge-danger', 'STAFF' => 'badge-info'][$user->roles] ?? 'badge-light' }}">
                                                    {{ ['ADMIN' => 'Admin', 'STAFF' => 'Staf', 'USER' => 'Peserta'][$user->roles] ?? '—' }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $user->roles === 'USER' ? $user->ujian_selesai : '—' }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('user.edit', $user) }}" class="btn btn-sm btn-info text-nowrap"><i class="fas fa-edit"></i> Ubah</a>
                                                    @unless ($user->is(auth()->user()))
                                                        <form action="{{ route('user.destroy', $user) }}" method="POST" class="ml-2"
                                                            onsubmit="return confirm('Hapus pengguna ini beserta riwayat ujiannya?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger text-nowrap"><i class="fas fa-trash"></i> Hapus</button>
                                                        </form>
                                                    @endunless
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pengguna yang cocok.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="float-right">{{ $users->links() }}</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
