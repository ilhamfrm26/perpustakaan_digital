@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
@php $rp = fn($n) => 'Rp'.number_format($n,0,',','.'); @endphp
<div class="head"><div><h1>Laporan</h1><p class="sub">Ringkasan peminjaman dan denda berdasarkan tanggal.</p></div></div>

<form class="filter" method="get">
    <label>Dari <input type="date" name="dari" value="{{ request('dari') }}"></label>
    <label>Sampai <input type="date" name="sampai" value="{{ request('sampai') }}"></label>
    <button class="btn ghost">Tampilkan</button>
</form>

<div class="cards">
    <div class="stat"><div class="n">{{ $ringkas['total'] }}</div><div class="l">Total peminjaman</div></div>
    <div class="stat"><div class="n">{{ $ringkas['dikembalikan'] }}</div><div class="l">Sudah dikembalikan</div><div class="h">{{ $ringkas['terlambat'] }} terlambat</div></div>
    <div class="stat"><div class="n">{{ $rp($ringkas['denda_lunas']) }}</div><div class="l">Denda lunas</div></div>
    <div class="stat"><div class="n">{{ $rp($ringkas['denda_belum']) }}</div><div class="l">Denda belum lunas</div></div>
</div>

<div class="card tbl">
@if($loans->isEmpty())<div class="empty">Tidak ada data pada rentang tanggal ini.</div>@else
<table>
    <tr><th>Tanggal</th><th>Buku</th><th>Peminjam</th><th>Status</th><th>Denda</th><th>Pembayaran</th></tr>
    @foreach($loans as $l)
    <tr><td>{{ $l->created_at->translatedFormat('d M Y') }}</td><td><b>{{ $l->book->judul }}</b></td><td>{{ $l->user->name }}</td>
        <td><span class="badge {{ $l->badge() }}">{{ $l->label() }}</span></td>
        <td>{{ $l->denda > 0 ? $rp($l->denda) : '-' }}</td>
        <td>@if($l->payment)<span class="badge {{ $l->payment->badge() }}">{{ $l->payment->label() }}</span>@else - @endif</td></tr>
    @endforeach
</table>
{{ $loans->links('pagination.simple') }}
@endif
</div>
@endsection
