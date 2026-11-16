<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Perpustakaan Sekolah</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="lg">
    <div class="lg-brand">Perpustakaan<small>Sekolah</small></div>

    <div class="lg-card">
        <h1>Masuk</h1>
        <p class="sub">Gunakan username dan password dari petugas perpustakaan.</p>

        @if($errors->any())
            <div class="flash error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" autofocus autocomplete="username">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" autocomplete="current-password">
            </div>
            <button class="btn" style="width:100%">Masuk</button>
        </form>
    </div>

    <div class="lg-demo">
        <span>Coba akun contoh:</span>
        <button type="button" onclick="isi('admin','admin123')">Admin</button>
        <button type="button" onclick="isi('petugas','petugas123')">Petugas</button>
        <button type="button" onclick="isi('pelajar','pelajar123')">Pelajar</button>
    </div>

    <div class="lg-shelf" aria-hidden="true">
        @foreach([[58,'#1f6f5c'],[78,'#d9b36a'],[64,'#2b5f9e'],[84,'#7fc4b2'],[52,'#a8741a'],[72,'#3a8f78'],[60,'#d9b36a'],[80,'#1f6f5c'],[66,'#2b5f9e'],[76,'#7fc4b2'],[54,'#a8741a'],[70,'#3a8f78']] as [$h,$c])
            <span style="height:{{ $h }}px;background:{{ $c }}"></span>
        @endforeach
    </div>
</div>

<script>
    function isi(u, p) {
        document.getElementById('username').value = u;
        document.getElementById('password').value = p;
    }
</script>
</body>
</html>