<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', ['categories' => Category::withCount('books')->orderBy('nama')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama' => 'required|string|max:100|unique:categories,nama'], [
            'nama.unique' => 'Kategori ini sudah ada.',
        ]);
        Category::create($data);

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function destroy(Category $category)
    {
        $category->delete(); // buku di kategori ini menjadi "tanpa kategori"

        return back()->with('success', 'Kategori dihapus.');
    }
}
