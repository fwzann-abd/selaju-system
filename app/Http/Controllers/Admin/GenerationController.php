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
        // Simple toggle: allow multiple generations to be active
        $generation->update(['is_active' => ! $generation->is_active]);

        $message = $generation->is_active ? 'Generasi berhasil diaktifkan.' : 'Generasi berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.generations.index')
            ->with('success', $message);
    }

    public function setAsCurrent(Generation $generation)
    {
        // Set this generation as current (default for registrations)
        // Deactivate current for all others
        Generation::where('id', '!=', $generation->id)->update(['is_current' => false]);
        $generation->update(['is_current' => true]);

        return redirect()
            ->route('admin.generations.index')
            ->with('success', 'Generasi "'.$generation->name.'" berhasil diset sebagai generasi saat ini.');
    }
}
