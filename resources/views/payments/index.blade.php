@extends('layouts.app')
@section('title', 'Denda & pembayaran')
@section('content')
@php $role = auth()->user()->role; $staff = auth()->user()->isStaff(); $rp = fn($n) => 'Rp'.number_format($n,0,',','.'); @endphp
<div class="head">
    <div><h1>Denda &amp; pembayaran</h1>
    <p class="sub">Denda Rp{{ number_format(config('perpustakaan.denda_per_hari'),0,',','.') }} per hari keterlambatan.</p></div>
</div>

@if($role === 'pelajar' && $payments->contains(fn($p) => $p->status !== 'lunas'))
<div class="note">
    <b>Cara membayar.</b> Tunai: serahkan ke petugas. Transfer: {{ config('perpustakaan.rekening') }}. QRIS: scan di meja petugas.
    Setelah memilih cara bayar, petugas akan mengonfirmasi dan status berubah menjadi Lunas.
</div>
@endif

<div class="card tbl">
@if($payments->isEmpty())
    <div class="empty">Tidak ada denda. {{ $staff ? '' : 'Kembalikan buku tepat waktu agar tetap begitu.' }}</div>
@else
<table>
    <tr><th>Buku</th>@if($staff)<th>Peminjam</th>@endif<th>Jumlah</th><th>Cara bayar</th><th>Status</th><th></th></tr>
    @foreach($payments as $p)
    <tr>
        <td><b>{{ $p->loan->book->judul }}</b><small>Jatuh tempo {{ $p->loan->tgl_jatuh_tempo?->translatedFormat('d M Y') }}, kembali {{ $p->loan->tgl_kembali?->translatedFormat('d M Y') }}</small></td>
        @if($staff)<td>{{ $p->user->name }}</td>@endif
        <td><b>{{ $rp($p->jumlah) }}</b></td>
        <td>{{ $p->metode ? ucfirst($p->metode) : '-' }}</td>
        <td><span class="badge {{ $p->badge() }}">{{ $p->label() }}</span>@if($p->dibayar_pada)<small>{{ $p->dibayar_pada->translatedFormat('d M Y H:i') }}</small>@endif</td>
        <td style="text-align:right">
            @if($role === 'pelajar' && $p->status === 'belum')
                <form class="inline" method="post" action="{{ route('payments.pay', $p) }}">@csrf
                    <select name="metode" class="sm" aria-label="Cara bayar"><option value="tunai">Tunai</option><option value="transfer">Transfer</option><option value="qris">QRIS</option></select>
                    <button class="btn sm">Bayar</button></form>
            @elseif($role === 'pelajar' && $p->status === 'menunggu')
                <span class="muted">Menunggu petugas</span>
            @endif
            @if($staff && $p->status !== 'lunas')
                <form class="inline" method="post" action="{{ route('payments.confirm', $p) }}">@csrf
                    <select name="metode" class="sm" aria-label="Cara bayar">@foreach(['tunai','transfer','qris'] as $m)<option value="{{ $m }}" @selected(($p->metode ?? 'tunai') === $m)>{{ ucfirst($m) }}</option>@endforeach</select>
                    <button class="btn sm ok">Konfirmasi lunas</button></form>
            @endif
        </td>
    </tr>
    @endforeach
</table>
{{ $payments->links('pagination.simple') }}
@endif
</div>
@endsection
