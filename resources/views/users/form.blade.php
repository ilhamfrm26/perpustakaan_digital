@extends('layouts.app')
@section('title', $user->exists ? 'Ubah pengguna' : 'Tambah pengguna')
@section('content')
<div class="head"><h1>{{ $user->exists ? 'Ubah pengguna' : 'Tambah pengguna' }}</h1></div>
<div class="card" style="max-width:720px">
<form method="post" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
    @csrf @if($user->exists) @method('PUT') @endif
    <div class="grid2">
        <div class="field"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
        <div class="field"><label for="username">Username</label><input id="username" name="username" value="{{ old('username', $user->username) }}" required><div class="hint">Huruf, angka, strip, dan garis bawah. Dipakai untuk masuk.</div></div>
        <div class="field"><label for="role">Peran</label>
            <select id="role" name="role">@foreach(['admin'=>'Admin','petugas'=>'Petugas','pelajar'=>'Pelajar'] as $k=>$v)<option value="{{ $k }}" @selected(old('role', $user->role ?: 'pelajar')===$k)>{{ $v }}</option>@endforeach</select></div>
        <div class="field"><label for="kelas">Kelas (untuk pelajar)</label><input id="kelas" name="kelas" value="{{ old('kelas', $user->kelas) }}" placeholder="Contoh: XI RPL"></div>
        <div class="field"><label for="password">Password</label><input id="password" type="password" name="password" {{ $user->exists ? '' : 'required' }} autocomplete="new-password">
            @if($user->exists)<div class="hint">Kosongkan jika tidak ingin mengganti password.</div>@else<div class="hint">Minimal 6 karakter.</div>@endif</div>
    </div>
    <button class="btn">Simpan pengguna</button> <a class="btn ghost" href="{{ route('users.index') }}">Batal</a>
</form>
</div>
@endsection
