@extends('layouts.app')
@section('title', $book->exists ? 'Ubah buku' : 'Tambah buku')
@section('content')
<div class="head"><h1>{{ $book->exists ? 'Ubah data buku' : 'Tambah buku' }}</h1></div>
<div class="card" style="max-width:720px">
<form method="post" action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}">
    @csrf @if($book->exists) @method('PUT') @endif
    <div class="field"><label for="judul">Judul buku</label><input id="judul" name="judul" value="{{ old('judul', $book->judul) }}" required></div>
    <div class="grid2">
        <div class="field"><label for="penulis">Penulis</label><input id="penulis" name="penulis" value="{{ old('penulis', $book->penulis) }}" required></div>
        <div class="field"><label for="penerbit">Penerbit</label><input id="penerbit" name="penerbit" value="{{ old('penerbit', $book->penerbit) }}"></div>
        <div class="field"><label for="category_id">Kategori</label>
            <select id="category_id" name="category_id"><option value="">Tanpa kategori</option>
            @foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $book->category_id) == $c->id)>{{ $c->nama }}</option>@endforeach</select></div>
        <div class="field"><label for="tahun">Tahun terbit</label><input id="tahun" type="number" name="tahun" value="{{ old('tahun', $book->tahun) }}"></div>
        <div class="field"><label for="stok">Jumlah stok</label><input id="stok" type="number" min="0" name="stok" value="{{ old('stok', $book->stok ?? 1) }}" required></div>
    </div>
    <button class="btn">Simpan buku</button> <a class="btn ghost" href="{{ route('books.index') }}">Batal</a>
</form>
</div>
@endsection
