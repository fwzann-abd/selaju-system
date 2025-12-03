<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerpossagarBook;
use App\Models\PerpossagarCategory;
use App\Models\PerpossagarAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;

class PerpossagarBookController extends Controller
{
    public function index()
    {
        $books = PerpossagarBook::with(['author', 'categories'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.perpossagar.books.index', compact('books'));
    }

    public function create()
    {
        $categories = PerpossagarCategory::orderBy('name')->get();
        return view('admin.perpossagar.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // quick pre-check for upload errors so we can return a clearer message
        if ($request->hasFile('filename') && !$request->file('filename')->isValid()) {
            $err = $request->file('filename')->getError();
            Log::warning('PerpossagarBookController::store - filename upload invalid', [
                'error' => $err,
                'name' => $request->file('filename')->getClientOriginalName() ?? null,
            ]);
            return back()->withInput()->withErrors(['filename' => 'File upload failed (code: ' . $err . ').']);
        }

        if ($request->hasFile('photo') && !$request->file('photo')->isValid()) {
            $err = $request->file('photo')->getError();
            Log::warning('PerpossagarBookController::store - photo upload invalid', [
                'error' => $err,
                'name' => $request->file('photo')->getClientOriginalName() ?? null,
            ]);
            return back()->withInput()->withErrors(['photo' => 'Image upload failed (code: ' . $err . ').']);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'language' => 'nullable|string|max:50',
            'color_hex' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'photo' => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'filename' => 'nullable|mimes:pdf|max:51200',
            'author_name' => 'nullable|string|max:255',
            'is_approved' => 'sometimes|boolean',
            'categories' => 'required|array|min:1',
            'categories.*' => 'uuid|exists:perpossagar_book_categories,uuid',
        ]);

        // Determine author: try to link to participant-author, otherwise accept an admin-provided author_name
        $user = Auth::user();
        $participantId = $user->participant_id ?? null;
        $authorUuid = null;
        $authorName = null;

        if ($participantId && DB::table('participants')->where('id', $participantId)->exists()) {
            $author = PerpossagarAuthor::firstOrCreate(
                ['participant_id' => $participantId],
                ['uuid' => Str::uuid()]
            );
            $authorUuid = $author->uuid;
        } else {
            // try to find a participant by the user's email as a fallback
            $found = DB::table('participants')->where('email', $user->email)->first();
            if ($found) {
                $participantId = $found->id;
                $author = PerpossagarAuthor::firstOrCreate(
                    ['participant_id' => $participantId],
                    ['uuid' => Str::uuid()]
                );
                $authorUuid = $author->uuid;
            } else {
                // no participant found; allow admin to provide an author_name instead
                if (!empty($data['author_name'])) {
                    $authorName = trim($data['author_name']);
                } else {
                    return back()->withInput()->withErrors(['author' => 'No participant record found for the current user. Please provide an author name or create a participant entry.']);
                }
            }
        }

        $slug = Str::slug($data['title']);
        $base = $slug;
        $i = 1;
        while (PerpossagarBook::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        $bookData = [
            'uuid' => Str::uuid(),
            'author_id' => $authorUuid, // may be null if admin provided author_name
            'author_name' => $authorName,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'slug' => $slug,
            'desc' => $data['desc'] ?? null,
            'language' => $data['language'] ?? 'id',
            'color_hex' => $data['color_hex'] ?? '#000000',
            'is_approved' => $data['is_approved'] ?? false,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $filename = Str::random(20) . '.' . $photoFile->getClientOriginalExtension();

            // Compress image to 200KB
            $image = Image::read($photoFile)
                ->scale(800, 1000)
                ->toJpeg(quality: 75);

            // Ensure file size is under 200KB
            $quality = 75;
            while ($image->toJpeg(quality: $quality)->filesize() > 204800 && $quality > 20) {
                $quality -= 5;
                $image = Image::read($photoFile)
                    ->scale(800, 1000)
                    ->toJpeg(quality: $quality);
            }

            $path = 'perpossagar/books';
            Storage::disk('public')->makeDirectory($path, 0755, true);
            Storage::disk('public')->put($path . '/' . $filename, (string) $image);

            $bookData['photo'] = 'assets/modules/' . $path . '/' . $filename;
        }

        // Handle PDF upload
        if ($request->hasFile('filename')) {
            $pdfPath = $request->file('filename')->store('perpossagar/books/pdf', 'public');
            // Log mime/size for debugging
            try {
                $mime = $request->file('filename')->getClientMimeType();
                $size = $request->file('filename')->getSize();
                Log::info('PerpossagarBookController::store - PDF uploaded', ['mime' => $mime, 'size' => $size]);
            } catch (\Throwable $e) {
                Log::debug('PerpossagarBookController::store - unable to read pdf mime/size', ['exception' => $e->getMessage()]);
            }
            $bookData['filename'] = 'assets/modules/' . $pdfPath;
        }

        $book = PerpossagarBook::create($bookData);

        // Attach categories
        if (!empty($data['categories'])) {
            $book->categories()->sync($data['categories']);
        }

        return redirect()->route('admin.perpossagar-books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $book = PerpossagarBook::where('uuid', $id)->with(['author', 'categories'])->firstOrFail();
        $categories = PerpossagarCategory::orderBy('name')->get();
        return view('admin.perpossagar.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $book = PerpossagarBook::where('uuid', $id)->firstOrFail();

        // quick pre-check for upload errors so we can return a clearer message
        if ($request->hasFile('filename') && !$request->file('filename')->isValid()) {
            $err = $request->file('filename')->getError();
            Log::warning('PerpossagarBookController::update - filename upload invalid', [
                'error' => $err,
                'name' => $request->file('filename')->getClientOriginalName() ?? null,
            ]);
            return back()->withInput()->withErrors(['filename' => 'File upload failed (code: ' . $err . ').']);
        }

        if ($request->hasFile('photo') && !$request->file('photo')->isValid()) {
            $err = $request->file('photo')->getError();
            Log::warning('PerpossagarBookController::update - photo upload invalid', [
                'error' => $err,
                'name' => $request->file('photo')->getClientOriginalName() ?? null,
            ]);
            return back()->withInput()->withErrors(['photo' => 'Image upload failed (code: ' . $err . ').']);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'language' => 'nullable|string|max:50',
            'color_hex' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'photo' => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'filename' => 'nullable|mimes:pdf|max:51200',
            'is_approved' => 'sometimes|boolean',
            'categories' => 'required|array|min:1',
            'categories.*' => 'uuid|exists:perpossagar_book_categories,uuid',
        ]);

        // Update slug if title changed
        if (($data['title'] ?? null) && $data['title'] !== $book->title) {
            $slug = Str::slug($data['title']);
            $base = $slug;
            $i = 1;
            while (PerpossagarBook::where('slug', $slug)->where('uuid', '!=', $book->uuid)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($book->photo) {
                $oldPhotoPath = str_replace('assets/modules/', '', $book->photo);
                if (Storage::disk('public')->exists($oldPhotoPath)) {
                    Storage::disk('public')->delete($oldPhotoPath);
                }
            }

            $photoFile = $request->file('photo');
            $filename = Str::random(20) . '.' . $photoFile->getClientOriginalExtension();

            // Compress image to 200KB
            $image = Image::read($photoFile)
                ->scale(800, 1000)
                ->toJpeg(quality: 75);

            // Ensure file size is under 200KB
            $quality = 75;
            while ($image->toJpeg(quality: $quality)->filesize() > 204800 && $quality > 20) {
                $quality -= 5;
                $image = Image::read($photoFile)
                    ->scale(800, 1000)
                    ->toJpeg(quality: $quality);
            }

            $path = 'perpossagar/books';
            Storage::disk('public')->makeDirectory($path, 0755, true);
            Storage::disk('public')->put($path . '/' . $filename, (string) $image);

            $data['photo'] = 'assets/modules/' . $path . '/' . $filename;
        }

        // Handle PDF upload
        if ($request->hasFile('filename')) {
            // Delete old PDF if exists
            if ($book->filename) {
                $oldPdfPath = str_replace('assets/modules/', '', $book->filename);
                if (Storage::disk('public')->exists($oldPdfPath)) {
                    Storage::disk('public')->delete($oldPdfPath);
                }
            }
            $pdfPath = $request->file('filename')->store('perpossagar/books/pdf', 'public');
            $data['filename'] = 'assets/modules/' . $pdfPath;
        }

        $book->update($data);

        // Sync categories
        if (isset($data['categories'])) {
            $book->categories()->sync($data['categories']);
        }

        return redirect()->route('admin.perpossagar-books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $book = PerpossagarBook::where('uuid', $id)->firstOrFail();
        $book->delete();

        return redirect()->route('admin.perpossagar-books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
