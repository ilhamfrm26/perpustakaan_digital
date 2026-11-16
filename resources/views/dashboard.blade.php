@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
<div class="head">
    <div><h1>Halo, {{ auth()->user()->name }}</h1>
    <p class="sub">{{ ['admin'=>'Pantau seluruh kegiatan perpustakaan di sini.','petugas'=>'Ini pekerjaan yang menunggu Anda hari ini.','pelajar'=>'Cari buku yang ingin dibaca, lalu ajukan pinjaman.'][auth()->user()->role] }}</p></div>
</div>

<div class="cards">
    @foreach($cards as [$label, $value, $hint])
        <div class="stat"><div class="n">{{ $value }}</div><div class="l">{{ $label }}</div><div class="h">{{ $hint }}</div></div>
    @endforeach
</div>

<div class="card">
    <h2>Alur peminjaman</h2>
    <ol class="steps">
        <li><b>Ajukan</b><span>Pelajar memilih buku di katalog lalu menekan Pinjam.</span></li>
        <li><b>Disetujui</b><span>Petugas menyetujui dan menyerahkan buku. Lama pinjam {{ config('perpustakaan.lama_pinjam') }} hari.</span></li>
        <li><b>Kembalikan</b><span>Pelajar membawa buku ke petugas. Petugas memeriksa dan menerimanya.</span></li>
        <li><b>Bayar denda</b><span>Jika terlambat, denda Rp{{ number_format(config('perpustakaan.denda_per_hari'),0,',','.') }} per hari. Bayar lalu petugas konfirmasi.</span></li>
    </ol>
</div>
@endsection
