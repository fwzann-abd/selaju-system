<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerpossagarAuthor;
use App\Models\PerpossagarBook;
use App\Models\PerpossagarBookLanguage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class PerpossagarBookController extends Controller
{
    /**
     * Display a listing of books with optional search and category filter.
     */
    public function index(Request $request)
    {
        $query = PerpossagarBook::with(['author', 'categories', 'languageOption'])
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
                'language_meta' => ($book->language_id || $book->language) ? [
                    'uuid' => $book->language_id,
                    'name' => $book->languageOption?->name,
                    'slug' => $book->languageOption?->slug ?? $book->language,
                ] : null,
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
        $book = PerpossagarBook::with(['author', 'categories', 'languageOption'])
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
                'language_meta' => ($book->language_id || $book->language) ? [
                    'uuid' => $book->language_id,
                    'name' => $book->languageOption?->name,
                    'slug' => $book->languageOption?->slug ?? $book->language,
                ] : null,
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
        $books = PerpossagarBook::with(['author', 'categories', 'languageOption'])
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
                    'language_meta' => ($book->language_id || $book->language) ? [
                        'uuid' => $book->language_id,
                        'name' => $book->languageOption?->name,
                        'slug' => $book->languageOption?->slug ?? $book->language,
                    ] : null,
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
        $random = filter_var($request->query('random', false), FILTER_VALIDATE_BOOLEAN);

        $query = PerpossagarBook::with(['author', 'categories', 'languageOption'])
            ->where('is_approved', true)
            ->orderBy('read_count', 'desc')
            ->orderBy('created_at', 'desc');

        $books = $query->limit($limit)->get();

        if ($random) {
            $poolLimit = max($limit * 3, $limit);
            $books = $query->limit($poolLimit)->get()->shuffle()->take($limit)->values();
        }

        $books = $books->map(function ($book) {
            return [
                'uuid' => $book->uuid,
                'title' => $book->title,
                'subtitle' => $book->subtitle,
                'slug' => $book->slug,
                'desc' => $book->desc,
                'photo' => $book->photo ? basename($book->photo) : null,
                'color_hex' => $book->color_hex,
                'author' => $book->author_display_name,
                'language_meta' => ($book->language_id || $book->language) ? [
                    'uuid' => $book->language_id,
                    'name' => $book->languageOption?->name,
                    'slug' => $book->languageOption?->slug ?? $book->language,
                ] : null,
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
     * Latest community works submitted by participants.
     */
    public function community(Request $request)
    {
        $limit = (int) $request->query('limit', 6);
        $limit = max(1, min($limit, 24));

        $books = PerpossagarBook::with(['author', 'categories', 'languageOption'])
            ->whereNotNull('author_id')
            ->where('is_approved', true)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($book) {
                return [
                    'uuid' => $book->uuid,
                    'title' => $book->title,
                    'subtitle' => $book->subtitle,
                    'slug' => $book->slug,
                    'desc' => $book->desc,
                    'language' => $book->language,
                    'language_meta' => ($book->language_id || $book->language) ? [
                        'uuid' => $book->language_id,
                        'name' => $book->languageOption?->name,
                        'slug' => $book->languageOption?->slug ?? $book->language,
                    ] : null,
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

        return response()->json([
            'data' => $books,
            'photo_path' => $this->photoBaseUrl(),
            'pdf_path' => $this->pdfBaseUrl(),
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

    /**
     * Return books created by the authenticated participant.
     */
    public function mine(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $author = PerpossagarAuthor::firstOrCreate(
            ['participant_id' => $user->getKey()],
            ['uuid' => (string) Str::uuid(), 'author_at' => now()]
        );

        $books = PerpossagarBook::with(['author', 'categories', 'languageOption'])
            ->where('author_id', $author->uuid)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($book) {
                return [
                    'uuid' => $book->uuid,
                    'title' => $book->title,
                    'subtitle' => $book->subtitle,
                    'slug' => $book->slug,
                    'desc' => $book->desc,
                    'language' => $book->language,
                    'language_meta' => ($book->language_id || $book->language) ? [
                        'uuid' => $book->language_id,
                        'name' => $book->languageOption?->name,
                        'slug' => $book->languageOption?->slug ?? $book->language,
                    ] : null,
                    'color_hex' => $book->color_hex,
                    'photo' => $book->photo ? basename($book->photo) : null,
                    'filename' => $book->filename ? basename($book->filename) : null,
                    'is_approved' => (bool) $book->is_approved,
                    'read_count' => $book->read_count ?? 0,
                    'author' => $book->author_display_name ?? $book->author_name,
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

        return response()->json([
            'data' => $books,
            'photo_path' => $this->photoBaseUrl(),
            'pdf_path' => $this->pdfBaseUrl(),
        ]);
    }

    /**
     * Allow authenticated participants to submit their own book entry.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'desc' => ['nullable', 'string'],
            'language_id' => ['required', 'uuid', 'exists:perpossagar_book_langs,uuid'],
            'color_hex' => ['nullable', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'filename' => ['required', 'mimes:pdf', 'max:10240'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['uuid', 'exists:perpossagar_book_categories,uuid'],
        ]);

        $language = PerpossagarBookLanguage::where('uuid', $validated['language_id'])->firstOrFail();

        $author = PerpossagarAuthor::firstOrCreate(
            ['participant_id' => $user->getKey()],
            ['uuid' => (string) Str::uuid(), 'author_at' => now()]
        );

        $slugBase = Str::slug($validated['title']) ?: Str::slug(Str::random(10));
        $slug = $slugBase;
        $counter = 1;
        while (PerpossagarBook::where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$counter++;
        }

        $bookData = [
            'uuid' => (string) Str::uuid(),
            'author_id' => $author->uuid,
            'author_name' => $user->name,
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'slug' => $slug,
            'desc' => $validated['desc'] ?? null,
            'language_id' => $language->uuid,
            'language' => $language->slug,
            'color_hex' => $validated['color_hex'] ?? '#0B4A84',
            'is_approved' => false,
            'published_at' => null,
        ];

        if ($request->hasFile('photo')) {
            $bookData['photo'] = $this->storePhotoFile($request->file('photo'));
        }

        $pdfFile = $request->file('filename');
        if (! $pdfFile) {
            return response()->json(['message' => 'File PDF diperlukan'], 422);
        }
        $bookData['filename'] = $this->storeDocumentFile($pdfFile);

        $book = PerpossagarBook::create($bookData);
        $book->categories()->sync($validated['categories']);
        $book->load(['author', 'categories']);

        return response()->json([
            'message' => 'Buku berhasil diajukan dan menunggu review admin.',
            'data' => [
                'uuid' => $book->uuid,
                'title' => $book->title,
                'subtitle' => $book->subtitle,
                'slug' => $book->slug,
                'desc' => $book->desc,
                'language' => $book->language,
                'language_meta' => ($book->language_id || $book->language) ? [
                    'uuid' => $book->language_id,
                    'name' => $book->languageOption?->name,
                    'slug' => $book->languageOption?->slug ?? $book->language,
                ] : null,
                'color_hex' => $book->color_hex,
                'photo' => $book->photo ? basename($book->photo) : null,
                'filename' => $book->filename ? basename($book->filename) : null,
                'is_approved' => (bool) $book->is_approved,
                'read_count' => $book->read_count ?? 0,
                'author' => $book->author_display_name ?? $book->author_name,
                'categories' => $book->categories->map(function ($cat) {
                    return [
                        'uuid' => $cat->uuid,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                    ];
                }),
                'created_at' => $book->created_at,
            ],
            'photo_path' => $this->photoBaseUrl(),
            'pdf_path' => $this->pdfBaseUrl(),
        ], 201);
    }

    protected function photoBaseUrl(): string
    {
        return rtrim(url('/assets/modules/perpossagar/books/image/'), '/').'/';
    }

    protected function pdfBaseUrl(): string
    {
        return rtrim(url('/assets/modules/perpossagar/books/pdf/'), '/').'/';
    }

    protected function storePhotoFile(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        $destination = public_path('assets/modules/perpossagar/books/image');
        if (! File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = Str::random(20).'.jpg';
        $fullPath = $destination.'/'.$filename;

        Image::load($file->getRealPath())
            ->width(800)
            ->height(1000)
            ->save($fullPath);

        return 'assets/modules/perpossagar/books/image/'.$filename;
    }

    protected function storeDocumentFile(UploadedFile $file): string
    {
        $destination = public_path('assets/modules/perpossagar/books/pdf');
        if (! File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = Str::random(20).'.'.$file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return 'assets/modules/perpossagar/books/pdf/'.$filename;
    }
}
