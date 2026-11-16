@extends('layouts.app')
@section('title', 'Katalog buku')
@section('content')
@php $staff = auth()->user()->isStaff(); @endphp
<div class="head">
    <div><h1>Katalog buku</h1><p class="sub">{{ $staff ? 'Kelola koleksi buku perpustakaan.' : 'Cari buku, lalu tekan Pinjam.' }}</p></div>
    @if($staff)<a class="btn" href="{{ route('books.create') }}">Tambah buku</a>@endif
</div>

<form class="filter" method="get">
    <input name="q" value="{{ request('q') }}" placeholder="Cari judul atau nama penulis">
    <select name="kategori">
        <option value="">Semua kategori</option>
        @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('kategori') == $c->id)>{{ $c->nama }}</option>@endforeach
    </select>
    <button class="btn ghost">Cari</button>
</form>

<div class="card tbl">
@if($books->isEmpty())
    <div class="empty">Buku tidak ditemukan. Coba kata kunci lain.</div>
@else
<table>
    <tr><th>Judul</th><th>Kategori</th><th>Tahun</th><th>Stok</th><th></th></tr>
    @foreach($books as $b)
    <tr>
        <td><b>{{ $b->judul }}</b><small>{{ $b->penulis }}{{ $b->penerbit ? ' · '.$b->penerbit : '' }}</small></td>
        <td>{{ $b->category->nama ?? 'Tanpa kategori' }}</td>
        <td>{{ $b->tahun ?? '-' }}</td>
        <td>@if($b->stok > 0)<span class="badge ok">{{ $b->stok }} tersedia</span>@else<span class="badge bad">Habis</span>@endif</td>
        <td style="text-align:right;white-space:nowrap">
            @if($staff)
                <a class="btn sm ghost" href="{{ route('books.edit', $b) }}">Ubah</a>
                <form class="inline" method="post" action="{{ route('books.destroy', $b) }}" onsubmit="return confirm('Hapus buku ini?')">@csrf @method('DELETE')<button class="btn sm bad">Hapus</button></form>
            @else
                <form class="inline" method="post" action="{{ route('loans.store', $b) }}">@csrf<button class="btn sm" @disabled($b->stok < 1)>Pinjam</button></form>
            @endif
        </td>
    </tr>
    @endforeach
</table>
{{ $books->links('pagination.simple') }}
@endif
</div>
@endsection
