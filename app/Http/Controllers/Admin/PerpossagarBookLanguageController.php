<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerpossagarBookLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PerpossagarBookLanguageController extends Controller
{
    public function index()
    {
        $languages = PerpossagarBookLanguage::orderBy('name')->paginate(20);
        return view('admin.perpossagar.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.perpossagar.languages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:20', 'unique:perpossagar_book_langs,slug'],
        ]);

        PerpossagarBookLanguage::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        return redirect()->route('admin.perpossagar-book-langs.index')->with('success', 'Bahasa buku berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $language = PerpossagarBookLanguage::where('uuid', $id)->firstOrFail();
        return view('admin.perpossagar.languages.edit', compact('language'));
    }

    public function update(Request $request, string $id)
    {
        $language = PerpossagarBookLanguage::where('uuid', $id)->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:20', 'unique:perpossagar_book_langs,slug,' . $language->uuid . ',uuid'],
        ]);

        $language->update($data);

        return redirect()->route('admin.perpossagar-book-langs.index')->with('success', 'Bahasa buku diperbarui.');
    }

    public function destroy(string $id)
    {
        $language = PerpossagarBookLanguage::where('uuid', $id)->firstOrFail();
        $language->delete();

        return redirect()->route('admin.perpossagar-book-langs.index')->with('success', 'Bahasa buku dihapus.');
    }
}
