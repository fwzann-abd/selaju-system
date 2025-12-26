<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Sejajan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SejajanController extends Controller
{
    /**
     * Display a listing of the shops.
     */
    public function index(Request $request)
    {
        $search = $request->query('q', '');

        $shops = Sejajan::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('account', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->with('account:uuid,name,username')
            ->withCount('products')
            ->latest()
            ->paginate(15);

        return view('admin.sejajan.index', [
            'shops' => $shops,
            'search' => $search,
            'pageTitle' => 'Daftar Toko Sejajan',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Sejajan', 'url' => null],
                ['label' => 'Toko', 'url' => null],
            ],
        ]);
    }

    /**
     * Show the form for creating a new shop.
     */
    public function create()
    {
        $participants = \App\Models\Account::select('uuid', 'name', 'username', 'email')
            ->whereDoesntHave('sejajans')
            ->orderBy('name')
            ->get();

        return view('admin.sejajan.create', [
            'participants' => $participants,
            'pageTitle' => 'Tambah Toko Sejajan',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Sejajan', 'url' => null],
                ['label' => 'Toko', 'url' => route('admin.sejajan.index')],
                ['label' => 'Tambah', 'url' => null],
            ],
        ]);
    }

    /**
     * Store a newly created shop in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,uuid|unique:sejajans,participant_id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:sejajans,slug',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/assets/modules/sejajan/mart', $filename);
            $validated['photo'] = $filename;
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Sejajan::create($validated);

        return redirect()->route('admin.sejajan.index')->with('success', 'Toko berhasil ditambahkan.');
    }

    /**
     * Display the specified shop.
     */
    public function show(Sejajan $sejajan)
    {
        $sejajan->load(['participant', 'products']);

        return view('admin.sejajan.show', [
            'shop' => $sejajan,
            'photoPath' => Helper::getPhotoBasePath(),
            'pageTitle' => 'Detail Toko: '.$sejajan->name,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Sejajan', 'url' => null],
                ['label' => 'Toko', 'url' => route('admin.sejajan.index')],
                ['label' => 'Detail', 'url' => null],
            ],
        ]);
    }

    /**
     * Show the form for editing the specified shop.
     */
    public function edit(Sejajan $sejajan)
    {
        $participants = Participant::select('id', 'name', 'username', 'email')
            ->where(function ($query) use ($sejajan) {
                $query->whereDoesntHave('sejajan')
                    ->orWhere('id', $sejajan->participant_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.sejajan.edit', [
            'shop' => $sejajan,
            'participants' => $participants,
            'photoPath' => Helper::getPhotoBasePath(),
            'pageTitle' => 'Edit Toko: '.$sejajan->name,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Sejajan', 'url' => null],
                ['label' => 'Toko', 'url' => route('admin.sejajan.index')],
                ['label' => 'Edit', 'url' => null],
            ],
        ]);
    }

    /**
     * Update the specified shop in storage.
     */
    public function update(Request $request, Sejajan $sejajan)
    {
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,uuid|unique:sejajans,participant_id,'.$sejajan->id,
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:sejajans,slug,'.$sejajan->id,
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($sejajan->photo) {
                Storage::delete('public/assets/modules/sejajan/mart/'.$sejajan->photo);
            }

            $file = $request->file('photo');
            $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/assets/modules/sejajan/mart', $filename);
            $validated['photo'] = $filename;
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $sejajan->update($validated);

        return redirect()->route('admin.sejajan.index')->with('success', 'Toko berhasil diperbarui.');
    }

    /**
     * Remove the specified shop from storage.
     */
    public function destroy(Sejajan $sejajan)
    {
        // Delete photo if exists
        if ($sejajan->photo) {
            Storage::delete('public/assets/modules/sejajan/mart/'.$sejajan->photo);
        }

        $sejajan->delete();

        return redirect()->route('admin.sejajan.index')->with('success', 'Toko berhasil dihapus.');
    }
}
