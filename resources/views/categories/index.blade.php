@extends('layouts.app')
@section('title', 'Kategori buku')
@section('content')
<div class="head"><div><h1>Kategori buku</h1><p class="sub">Kelompokkan buku agar mudah dicari.</p></div></div>
<form class="filter" method="post" action="{{ route('categories.store') }}">
    @csrf
    <input name="nama" placeholder="Nama kategori baru" required>
    <button class="btn">Tambah kategori</button>
</form>
<div class="card tbl">
@if($categories->isEmpty())<div class="empty">Belum ada kategori. Tambahkan yang pertama di atas.</div>@else
<table>
    <tr><th>Kategori</th><th>Jumlah judul</th><th></th></tr>
    @foreach($categories as $c)
    <tr><td><b>{{ $c->nama }}</b></td><td>{{ $c->books_count }}</td>
        <td style="text-align:right"><form class="inline" method="post" action="{{ route('categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori ini? Bukunya tidak ikut terhapus.')">@csrf @method('DELETE')<button class="btn sm bad">Hapus</button></form></td></tr>
    @endforeach
</table>@endif
</div>
@endsection
