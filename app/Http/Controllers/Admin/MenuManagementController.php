<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuManagementController extends Controller
{
    /**
     * Display menus list.
     */
    public function index(): View
    {
        $menus = Menu::with('modules')->orderBy('row_order')->paginate(15);

        return view('admin.menus.index', [
            'menus' => $menus,
            'pageTitle' => 'Manajemen Menu',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Menu', 'url' => ''],
            ],
        ]);
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create(): View
    {
        return view('admin.menus.create', [
            'pageTitle' => 'Tambah Menu',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Menu', 'url' => route('admin.menus.index')],
                ['label' => 'Tambah Menu', 'url' => ''],
            ],
        ]);
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:menus',
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'row_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        Menu::create($request->all());

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Menu $menu): View
    {
        return view('admin.menus.edit', [
            'menu' => $menu,
            'pageTitle' => 'Edit Menu',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Menu', 'url' => route('admin.menus.index')],
                ['label' => 'Edit Menu', 'url' => ''],
            ],
        ]);
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:menus,code,'.$menu->id,
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'row_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $menu->update($request->all());

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}
