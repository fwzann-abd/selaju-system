<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        return Book::with(['author', 'categories', 'reviews'])->get();
    }

    public function show($uuid)
    {
        return Book::with(['author', 'categories', 'reviews'])
            ->where('uuid', $uuid)->firstOrFail();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'author_id'     => 'required|exists:authors,uuid',
            'title'         => 'required|string|max:255',
            'subtitle'      => 'nullable|string|max:255',
            'desc'          => 'nullable|string',
            'language'      => 'required|string|max:50',
            'photo'         => 'nullable|string',
            'color_hex'     => 'nullable|string|max:10',
            'filename'      => 'nullable|string',
            'categories'    => 'nullable|array',
            'categories.*'  => 'exists:books_category,uuid'
        ]);

        $book = Book::create([
            'uuid'        => Str::uuid(),
            'author_id'   => $validated['author_id'],
            'title'       => $validated['title'],
            'slug'        => Str::slug($validated['title']),
            'subtitle'    => $validated['subtitle'] ?? null,
            'desc'        => $validated['desc'] ?? null,
            'language'    => $validated['language'],
            'photo'       => $validated['photo'] ?? null,
            'color_hex'   => $validated['color_hex'] ?? null,
            'filename'    => $validated['filename'] ?? null,
            'is_approved' => false,
        ]);

        // Pivot
        if (!empty($validated['categories'])) {
            $book->categories()->sync($validated['categories']);
        }

        return response()->json($book->load(['author', 'categories']), 201);
    }

    public function update(Request $request, $uuid)
    {
        $book = Book::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'author_id'     => 'sometimes|exists:authors,uuid',
            'title'         => 'sometimes|string|max:255',
            'subtitle'      => 'nullable|string|max:255',
            'desc'          => 'nullable|string',
            'language'      => 'sometimes|string|max:50',
            'photo'         => 'nullable|string',
            'color_hex'     => 'nullable|string|max:10',
            'filename'      => 'nullable|string',
            'categories'    => 'nullable|array',
            'categories.*'  => 'exists:books_category,uuid'
        ]);

        // Hanya update field yang ada
        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $book->update($validated);

        if (isset($validated['categories'])) {
            $book->categories()->sync($validated['categories']);
        }

        return response()->json($book->load(['author', 'categories']));
    }

    public function destroy($uuid)
    {
        $book = Book::where('uuid', $uuid)->firstOrFail();
        $book->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
