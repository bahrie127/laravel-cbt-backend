<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->withCount(['ujians as ujian_selesai' => fn ($q) => $q->whereNotNull('selesai_at')])
            ->when($request->input('name'), fn ($q, $cari) => $q->where(fn ($w) => $w->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%")))
            ->when($request->input('roles'), fn ($q, $roles) => $q->where('roles', $roles))
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('pages.users.form', ['user' => new User(['roles' => User::PESERTA])]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('pages.users.form', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($user->is($request->user()) && $data['roles'] !== User::ADMIN) {
            return back()->with('error', 'Anda tidak dapat menurunkan peran akun sendiri.');
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
