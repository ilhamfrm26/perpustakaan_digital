<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') · Perpustakaan Sekolah</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php $u = auth()->user(); $role = $u->role; @endphp
<div class="app">
    <aside class="side">
        <div class="brand">Perpustakaan<small>Sekolah</small></div>
        <a class="nav {{ request()->routeIs('dashboard') ? 'on' : '' }}" href="{{ route('dashboard') }}">Beranda</a>
        <a class="nav {{ request()->routeIs('books.*') ? 'on' : '' }}" href="{{ route('books.index') }}">Katalog buku</a>
        <a class="nav {{ request()->routeIs('loans.*') ? 'on' : '' }}" href="{{ route('loans.index') }}">{{ $role === 'pelajar' ? 'Pinjaman saya' : 'Peminjaman' }}</a>
        <a class="nav {{ request()->routeIs('payments.*') ? 'on' : '' }}" href="{{ route('payments.index') }}">Denda &amp; pembayaran</a>
        @if($u->isStaff())
            <a class="nav {{ request()->routeIs('categories.*') ? 'on' : '' }}" href="{{ route('categories.index') }}">Kategori buku</a>
        @endif
        @if($role === 'admin')
            <a class="nav {{ request()->routeIs('users.*') ? 'on' : '' }}" href="{{ route('users.index') }}">Pengguna</a>
            <a class="nav {{ request()->routeIs('reports.*') ? 'on' : '' }}" href="{{ route('reports.index') }}">Laporan</a>
        @endif
        <div class="me">
            <div><b>{{ $u->name }}</b>{{ $u->roleLabel() }}</div>
            <form method="post" action="{{ route('logout') }}">@csrf<button>Keluar</button></form>
        </div>
    </aside>
    <main class="main">
        @if(session('success'))<div class="flash success" role="status">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="flash error" role="alert">{{ session('error') }}</div>@endif
        @if($errors->any())
            <div class="flash error" role="alert"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
