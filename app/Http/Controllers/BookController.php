<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private function rules(): array
    {
        return [
            'judul'       => 'required|string|max:255',
            'penulis'     => 'required|string|max:255',
            'penerbit'    => 'nullable|string|max:255',
            'tahun'       => 'nullable|integer|min:1900|max:'.date('Y'),
            'category_id' => 'nullable|exists:categories,id',
            'stok'        => 'required|integer|min:0',
        ];
    }

    public function index(Request $request)
    {
        $books = Book::with('category')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('judul', 'like', "%$s%")->orWhere('penulis', 'like', "%$s%")))
            ->when($request->kategori, fn ($q, $k) => $q->where('category_id', $k))
            ->orderBy('judul')
            ->simplePaginate(10)
            ->withQueryString();

        return view('books.index', [
            'books'      => $books,
            'categories' => Category::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('books.form', ['book' => new Book, 'categories' => Category::orderBy('nama')->get()]);
    }

    public function store(Request $request)
    {
        Book::create($request->validate($this->rules()));

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        return view('books.form', ['book' => $book, 'categories' => Category::orderBy('nama')->get()]);
    }

    public function update(Request $request, Book $book)
    {
        $book->update($request->validate($this->rules()));

        return redirect()->route('books.index')->with('success', 'Data buku diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->loans()->exists()) {
            return back()->with('error', 'Buku ini punya riwayat peminjaman, jadi tidak bisa dihapus. Ubah stok menjadi 0 jika tidak ingin dipinjam.');
        }
        $book->delete();

        return back()->with('success', 'Buku dihapus.');
    }
}
