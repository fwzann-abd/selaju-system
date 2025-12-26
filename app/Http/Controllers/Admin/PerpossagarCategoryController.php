<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerpossagarCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PerpossagarCategoryController extends Controller
{
    public function index()
    {
        $categories = PerpossagarCategory::orderBy('name')->paginate(15);

        return view('admin.perpossagar.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.perpossagar.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:perpossagar_book_categories,name',
        ]);

        PerpossagarCategory::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
        ]);

        return redirect()->route('admin.perpossagar-categories.index')->with('success', 'Kategori buku berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $category = PerpossagarCategory::where('uuid', $id)->firstOrFail();

        return view('admin.perpossagar.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = PerpossagarCategory::where('uuid', $id)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:perpossagar_book_categories,name,'.$category->uuid.',uuid',
        ]);

        $category->update($data);

        return redirect()->route('admin.perpossagar-categories.index')->with('success', 'Kategori buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = PerpossagarCategory::where('uuid', $id)->firstOrFail();
        $category->delete();

        return redirect()->route('admin.perpossagar-categories.index')->with('success', 'Kategori buku berhasil dihapus.');
    }
}
