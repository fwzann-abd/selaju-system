<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GenerationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $generations = Generation::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('start_years', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.generations.index', [
            'generations' => $generations,
            'search' => $search,
            'pageTitle' => 'Daftar Generasi',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => '#'],
                ['label' => 'Generasi', 'url' => ''],
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.generations.create', [
            'pageTitle' => 'Tambah Generasi',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => '#'],
                ['label' => 'Generasi', 'url' => route('admin.generations.index')],
                ['label' => 'Tambah', 'url' => ''],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_years' => ['required', 'integer', 'min:1900'],
            'end_years' => ['required', 'integer', 'min:1900'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        Generation::create($data);

        return redirect()
            ->route('admin.generations.index')
            ->with('success', 'Generasi berhasil ditambahkan.');
    }

    public function edit(Generation $generation): View
    {
        return view('admin.generations.edit', [
            'generation' => $generation,
            'pageTitle' => 'Edit Generasi',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => '#'],
                ['label' => 'Generasi', 'url' => route('admin.generations.index')],
                ['label' => 'Edit', 'url' => ''],
            ],
        ]);
    }

    public function update(Request $request, Generation $generation)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_years' => ['required', 'integer', 'min:1900'],
            'end_years' => ['required', 'integer', 'min:1900'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        // If setting this generation as active, deactivate all others
        if ($data['is_active']) {
            Generation::where('id', '!=', $generation->id)->update(['is_active' => false]);
        }

        $generation->update($data);

        return redirect()
            ->route('admin.generations.index')
            ->with('success', 'Generasi berhasil diperbarui.');
    }

    public function destroy(Generation $generation)
    {
        $generation->delete();

        return redirect()
            ->route('admin.generations.index')
            ->with('success', 'Generasi berhasil dihapus.');
    }

    public function toggleActive(Generation $generation)
    {
        // If generation is not active, set it as active and deactivate others
        if (!$generation->is_active) {
            Generation::where('id', '!=', $generation->id)->update(['is_active' => false]);
            $generation->update(['is_active' => true]);
            $message = 'Generasi berhasil diaktifkan.';
        } else {
            // If already active, deactivate it
            $generation->update(['is_active' => false]);
            $message = 'Generasi berhasil dinonaktifkan.';
        }

        return redirect()
            ->route('admin.generations.index')
            ->with('success', $message);
    }
}
