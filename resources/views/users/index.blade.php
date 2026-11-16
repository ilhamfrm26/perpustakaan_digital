@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="head"><div><h1>Pengguna</h1><p class="sub">Atur akun admin, petugas, dan pelajar.</p></div>
    <a class="btn" href="{{ route('users.create') }}">Tambah pengguna</a></div>
<form class="filter" method="get">
    <select name="role" onchange="this.form.submit()">
        <option value="">Semua peran</option>
        @foreach(['admin'=>'Admin','petugas'=>'Petugas','pelajar'=>'Pelajar'] as $k=>$v)<option value="{{ $k }}" @selected(request('role')===$k)>{{ $v }}</option>@endforeach
    </select>
</form>
<div class="card tbl">
<table>
    <tr><th>Nama</th><th>Username</th><th>Peran</th><th>Kelas</th><th></th></tr>
    @foreach($users as $x)
    <tr><td><b>{{ $x->name }}</b></td><td>{{ $x->username }}</td>
        <td><span class="badge {{ ['admin'=>'info','petugas'=>'warn','pelajar'=>'ok'][$x->role] }}">{{ $x->roleLabel() }}</span></td>
        <td>{{ $x->kelas ?? '-' }}</td>
        <td style="text-align:right;white-space:nowrap">
            <a class="btn sm ghost" href="{{ route('users.edit', $x) }}">Ubah</a>
            <form class="inline" method="post" action="{{ route('users.destroy', $x) }}" onsubmit="return confirm('Hapus pengguna ini?')">@csrf @method('DELETE')<button class="btn sm bad">Hapus</button></form>
        </td></tr>
    @endforeach
</table>
{{ $users->links('pagination.simple') }}
</div>
@endsection
