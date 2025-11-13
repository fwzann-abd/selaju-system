<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolModuleController extends Controller
{
    public function index(): View
    {
        $menus = Menu::where('status', true)->orderBy('row_order')->get();
        $module = Module::firstWhere('identifiers', 'schools');

        if (! $module) {
            $module = new Module([
                'identifiers' => 'schools',
                'name' => 'School',
                'url' => '/admin/schools',
                'icon' => null,
                'row_order' => 1,
                'is_active' => true,
            ]);
        }

        return view('admin.schools.config', [
            'menus' => $menus,
            'module' => $module,
            'pageTitle' => 'School Module',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Pengaturan', 'url' => '#'],
                ['label' => 'School Module', 'url' => ''],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $existingId = Module::where('identifiers', $request->input('identifiers'))->value('id');

        $validated = $request->validate([
            'menu_id' => ['required', 'exists:menus,id'],
            'identifiers' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modules', 'identifiers')->ignore($existingId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'row_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        Module::updateOrCreate(
            ['identifiers' => $validated['identifiers']],
            $validated
        );

        return redirect()
            ->route('admin.schools.config')
            ->with('success', 'Konfigurasi module School berhasil disimpan.');
    }
}
