<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleManagementController extends Controller
{
    /**
     * Display modules list.
     */
    public function index(): View
    {
        $modules = Module::with('menu')->orderBy('row_order')->paginate(15);

        return view('admin.modules.index', [
            'modules' => $modules,
            'pageTitle' => 'Manajemen Module',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Module', 'url' => ''],
            ],
        ]);
    }

    /**
     * Show the form for creating a new module.
     */
    public function create(): View
    {
        $menus = Menu::where('status', true)->orderBy('row_order')->get();

        return view('admin.modules.create', [
            'menus' => $menus,
            'pageTitle' => 'Tambah Module',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Module', 'url' => route('admin.modules.index')],
                ['label' => 'Tambah Module', 'url' => ''],
            ],
        ]);
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'identifiers' => 'required|string|max:255|unique:modules',
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'row_order' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        Module::create($request->all());

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module): View
    {
        $menus = Menu::where('status', true)->orderBy('row_order')->get();

        return view('admin.modules.edit', [
            'module' => $module,
            'menus' => $menus,
            'pageTitle' => 'Edit Module',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'Manajemen Module', 'url' => route('admin.modules.index')],
                ['label' => 'Edit Module', 'url' => ''],
            ],
        ]);
    }

    /**
     * Update the specified module in storage.
     */
    public function update(Request $request, Module $module)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'identifiers' => 'required|string|max:255|unique:modules,identifiers,'.$module->id,
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'row_order' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        $module->update($request->all());

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module berhasil diperbarui.');
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module berhasil dihapus.');
    }
}
