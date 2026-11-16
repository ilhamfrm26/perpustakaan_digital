@extends('layouts.app')
@section('title', auth()->user()->role === 'pelajar' ? 'Pinjaman saya' : 'Peminjaman')
@section('content')
@php $role = auth()->user()->role; $staff = auth()->user()->isStaff(); $rp = fn($n) => 'Rp'.number_format($n,0,',','.'); @endphp
<div class="head">
    <div><h1>{{ $staff ? 'Peminjaman' : 'Pinjaman saya' }}</h1>
    <p class="sub">{{ $staff ? 'Setujui pengajuan dan terima buku yang dikembalikan.' : 'Lihat status pinjaman dan batas waktu pengembalian.' }}</p></div>
    @unless($staff)<a class="btn" href="{{ route('books.index') }}">Cari buku</a>@endunless
</div>

@if($staff)
<form class="filter" method="get">
    <select name="status" onchange="this.form.submit()">
        <option value="">Semua status</option>
        @foreach(['diajukan'=>'Menunggu persetujuan','dipinjam'=>'Sedang dipinjam','menunggu_kembali'=>'Menunggu pengembalian','dikembalikan'=>'Sudah dikembalikan','ditolak'=>'Ditolak'] as $k=>$v)
            <option value="{{ $k }}" @selected($status===$k)>{{ $v }}</option>
        @endforeach
    </select>
</form>
@endif

<div class="card tbl">
@if($loans->isEmpty())
    <div class="empty">{{ $staff ? 'Belum ada data peminjaman.' : 'Anda belum meminjam buku. Mulai dari katalog.' }}</div>
@else
<table>
    <tr><th>Buku</th>@if($staff)<th>Peminjam</th>@endif<th>Pinjam</th><th>Jatuh tempo</th><th>Status</th><th>Denda</th><th></th></tr>
    @foreach($loans as $l)
    @php $dendaJalan = $l->aktif() ? $l->hitungDenda() : $l->denda; @endphp
    <tr>
        <td><b>{{ $l->book->judul }}</b></td>
        @if($staff)<td>{{ $l->user->name }}<small>{{ $l->user->kelas }}</small></td>@endif
        <td>{{ $l->tgl_pinjam?->translatedFormat('d M Y') ?? '-' }}</td>
        <td>{{ $l->tgl_jatuh_tempo?->translatedFormat('d M Y') ?? '-' }}
            @if($l->aktif() && $dendaJalan > 0)<small class="late">Terlambat</small>@endif</td>
        <td><span class="badge {{ $l->badge() }}">{{ $l->label() }}</span></td>
        <td>@if($dendaJalan > 0)<span class="late">{{ $rp($dendaJalan) }}</span>@else - @endif</td>
        <td style="text-align:right;white-space:nowrap">
            @if($role === 'pelajar' && $l->status === 'dipinjam')
                <form class="inline" method="post" action="{{ route('loans.requestReturn', $l) }}">@csrf<button class="btn sm ghost">Kembalikan</button></form>
            @endif
            @if($staff && $l->status === 'diajukan')
                <form class="inline" method="post" action="{{ route('loans.approve', $l) }}">@csrf<button class="btn sm ok">Setujui</button></form>
                <form class="inline" method="post" action="{{ route('loans.reject', $l) }}" onsubmit="return confirm('Tolak pengajuan ini?')">@csrf<button class="btn sm bad">Tolak</button></form>
            @endif
            @if($staff && $l->aktif())
                <form class="inline" method="post" action="{{ route('loans.return', $l) }}" onsubmit="return confirm('Terima buku ini kembali?')">@csrf<button class="btn sm">Terima buku</button></form>
            @endif
        </td>
    </tr>
    @endforeach
</table>
{{ $loans->links('pagination.simple') }}
@endif
</div>
@endsection
