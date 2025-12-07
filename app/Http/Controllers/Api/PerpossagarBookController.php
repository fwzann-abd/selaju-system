<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerpossagarBook;
use Illuminate\Http\Request;

class PerpossagarBookController extends Controller
{
    /**
     * Display a listing of books with optional search and category filter.
     */
    public function index(Request $request)
    {
        $query = PerpossagarBook::with(['author', 'categories'])
            ->where('is_approved', true);

        // Search by title or author name
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->whereHas('participant', function ($participantQuery) use ($search) {
                            $participantQuery->where('name', 'like', "%{$search}%");
                        });
                    });
            });
        }

        // Filter by category
        if ($categoryId = $request->query('category_id')) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('perpossagar_book_categories.uuid', $categoryId);
            });
        }

        $books = $query->orderBy('created_at', 'desc')->get();

        // Transform books to include author_display_name and relative paths
        $books = $books->map(function ($book) {
            return [
                'uuid' => $book->uuid,
                'title' => $book->title,
                'subtitle' => $book->subtitle,
                'slug' => $book->slug,
                'desc' => $book->desc,
                'language' => $book->language,
                'color_hex' => $book->color_hex,
                'photo' => $book->photo ? basename($book->photo) : null,
                'filename' => $book->filename ? basename($book->filename) : null,
                'is_approved' => $book->is_approved,
                'read_count' => $book->read_count ?? 0,
                'author' => $book->author_display_name,
                'categories' => $book->categories->map(function ($cat) {
                    return [
                        'uuid' => $cat->uuid,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                    ];
                }),
                'created_at' => $book->created_at,
            ];
        });

        $photoPath = rtrim(url('/assets/modules/perpossagar/books/image/'), '/').'/';
        $pdfPath = rtrim(url('/assets/modules/perpossagar/books/pdf/'), '/').'/';

        return response()->json([
            'data' => $books,
            'photo_path' => $photoPath,
            'pdf_path' => $pdfPath,
        ]);
    }

    /**
     * Display the specified book.
     */
    public function show($uuid)
    {
        $book = PerpossagarBook::with(['author', 'categories'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $photoPath = rtrim(url('/assets/modules/perpossagar/books/image/'), '/').'/';
        $pdfPath = rtrim(url('/assets/modules/perpossagar/books/pdf/'), '/').'/';

        return response()->json([
            'data' => [
                'uuid' => $book->uuid,
                'title' => $book->title,
                'subtitle' => $book->subtitle,
                'slug' => $book->slug,
                'desc' => $book->desc,
                'language' => $book->language,
                'color_hex' => $book->color_hex,
                'photo' => $book->photo ? basename($book->photo) : null,
                'filename' => $book->filename ? basename($book->filename) : null,
                'is_approved' => $book->is_approved,
                'read_count' => $book->read_count ?? 0,
                'author' => $book->author_display_name,
                'categories' => $book->categories->map(function ($cat) {
                    return [
                        'uuid' => $cat->uuid,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                    ];
                }),
                'created_at' => $book->created_at,
            ],
            'photo_path' => $photoPath,
            'pdf_path' => $pdfPath,
        ]);
    }

    /**
     * Get hero books for home page
     */
    public function hero()
    {
        $books = PerpossagarBook::with(['author', 'categories'])
            ->where('is_approved', true)
            ->where('is_hero', true)
            ->orderBy('hero_order')
            ->get()
            ->map(function ($book) {
                return [
                    'uuid' => $book->uuid,
                    'title' => $book->title,
                    'subtitle' => $book->subtitle,
                    'slug' => $book->slug,
                    'desc' => $book->desc,
                    'photo' => $book->photo ? basename($book->photo) : null,
                    'color_hex' => $book->color_hex,
                    'author' => $book->author_display_name,
                    'read_count' => $book->read_count ?? 0,
                ];
            });

        $photoPath = rtrim(url('/assets/modules/perpossagar/books/image/'), '/').'/';

        return response()->json([
            'data' => $books,
            'photo_path' => $photoPath,
        ]);
    }

    /**
     * Get popular books sorted by read_count
     */
    public function popular(Request $request)
    {
        $limit = $request->query('limit', 10);

        $books = PerpossagarBook::with(['author', 'categories'])
            ->where('is_approved', true)
            ->orderBy('read_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($book) {
                return [
                    'uuid' => $book->uuid,
                    'title' => $book->title,
                    'subtitle' => $book->subtitle,
                    'slug' => $book->slug,
                    'desc' => $book->desc,
                    'photo' => $book->photo ? basename($book->photo) : null,
                    'color_hex' => $book->color_hex,
                    'author' => $book->author_display_name,
                    'read_count' => $book->read_count ?? 0,
                    'categories' => $book->categories->map(function ($cat) {
                        return [
                            'uuid' => $cat->uuid,
                            'name' => $cat->name,
                            'slug' => $cat->slug,
                        ];
                    }),
                ];
            });

        $photoPath = rtrim(url('/assets/modules/perpossagar/books/image/'), '/').'/';

        return response()->json([
            'data' => $books,
            'photo_path' => $photoPath,
        ]);
    }

    /**
     * Increment read count (for both guest and authenticated users)
     */
    public function incrementReadCount($uuid)
    {
        $book = PerpossagarBook::where('uuid', $uuid)->firstOrFail();
        $book->increment('read_count');

        return response()->json([
            'success' => true,
            'read_count' => $book->read_count,
        ]);
    }
}
