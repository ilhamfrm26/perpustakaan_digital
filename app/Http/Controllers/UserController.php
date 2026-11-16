<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->orderBy('role')->orderBy('name')->simplePaginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|alpha_dash|max:50|unique:users,username',
            'role'     => ['required', Rule::in(['admin', 'petugas', 'pelajar'])],
            'kelas'    => 'nullable|string|max:50',
            'password' => 'required|string|min:6',
        ]);
        User::create($data);

        return redirect()->route('users.index')->with('success', 'Pengguna ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => ['required', 'alpha_dash', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'petugas', 'pelajar'])],
            'kelas'    => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);
        if (blank($data['password'] ?? null)) {
            unset($data['password']); // kosong = password tidak diubah
        }
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang dipakai.');
        }
        if ($user->loans()->exists()) {
            return back()->with('error', 'Pengguna ini punya riwayat peminjaman, jadi tidak bisa dihapus.');
        }
        $user->delete();

        return back()->with('success', 'Pengguna dihapus.');
    }
}
