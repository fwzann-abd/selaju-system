<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebexEkskul;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebexEkskulController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $ekskuls = WebexEkskul::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.webex.ekskul.index', [
            'ekskuls' => $ekskuls,
            'search' => $search,
            'pageTitle' => 'Daftar Ekskul',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Webex', 'url' => '#'],
                ['label' => 'Ekskul', 'url' => ''],
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.webex.ekskul.create', [
            'pageTitle' => 'Tambah Ekskul',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Webex', 'url' => '#'],
                ['label' => 'Ekskul', 'url' => route('admin.webex.ekskul.index')],
                ['label' => 'Tambah', 'url' => ''],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('webex_ekskuls', 'slug')],
            'bio' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'url'],
        ]);

        $data['slug'] = $this->formatSlug($data['slug']);

        WebexEkskul::create($data);

        return redirect()
            ->route('admin.webex.ekskul.index')
            ->with('success', 'Ekskul berhasil ditambahkan.');
    }

    public function edit(WebexEkskul $ekskul): View
    {
        return view('admin.webex.ekskul.edit', [
            'ekskul' => $ekskul,
            'pageTitle' => 'Edit Ekskul',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Webex', 'url' => '#'],
                ['label' => 'Ekskul', 'url' => route('admin.webex.ekskul.index')],
                ['label' => 'Edit', 'url' => ''],
            ],
        ]);
    }

    public function update(Request $request, WebexEkskul $ekskul)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('webex_ekskuls', 'slug')->ignore($ekskul->id)],
            'bio' => ['nullable', 'string'],
            'logo' => ['nullable', 'string', 'url'],
        ]);

        $data['slug'] = $this->formatSlug($data['slug']);

        $ekskul->update($data);

        return redirect()
            ->route('admin.webex.ekskul.index')
            ->with('success', 'Ekskul berhasil diperbarui.');
    }

    public function destroy(WebexEkskul $ekskul)
    {
        $ekskul->delete();

        return redirect()
            ->route('admin.webex.ekskul.index')
            ->with('success', 'Ekskul berhasil dihapus.');
    }

    private function formatSlug(string $value): string
    {
        $slug = Str::slug($value);

        return $slug !== '' ? $slug : Str::uuid()->toString();
    }
}
