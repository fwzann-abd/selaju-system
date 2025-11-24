<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get menu IDs
        $dashboardMenu = Menu::where('code', 'dashboard')->first();
        $kontenMenu = Menu::where('code', 'konten')->first();
        $pengaturanMenu = Menu::where('code', 'pengaturan')->first();

        $modules = [
            // Dashboard Modules
            [
                'menu_id' => $dashboardMenu?->id,
                'identifiers' => 'dashboard-main',
                'name' => 'Dashboard Utama',
                'url' => '/admin',
                'icon' => 'fa-home',
                'row_order' => 1,
                'is_active' => true,
            ],
            [
                'menu_id' => $dashboardMenu?->id,
                'identifiers' => 'dashboard-analytics',
                'name' => 'Analytics',
                'url' => '/admin/analytics',
                'icon' => 'fa-chart-bar',
                'row_order' => 2,
                'is_active' => true,
            ],
            // Konten Modules
            [
                'menu_id' => $kontenMenu?->id,
                'identifiers' => 'artikel-list',
                'name' => 'Artikel',
                'url' => '/admin/articles',
                'icon' => 'fa-newspaper',
                'row_order' => 1,
                'is_active' => true,
            ],
            [
                'menu_id' => $kontenMenu?->id,
                'identifiers' => 'kategori-artikel',
                'name' => 'Kategori Artikel',
                'url' => '/admin/article-categories',
                'icon' => 'fa-folder',
                'row_order' => 2,
                'is_active' => true,
            ],
            // Pengaturan Modules
            [
                'menu_id' => $pengaturanMenu?->id,
                'identifiers' => 'user-management',
                'name' => 'Manajemen Pengguna',
                'url' => '/admin/users',
                'icon' => 'fa-users',
                'row_order' => 1,
                'is_active' => true,
            ],
            [
                'menu_id' => $pengaturanMenu?->id,
                'identifiers' => 'menu-management',
                'name' => 'Manajemen Menu',
                'url' => '/admin/menus',
                'icon' => 'fa-bars',
                'row_order' => 2,
                'is_active' => true,
            ],
            [
                'menu_id' => $pengaturanMenu?->id,
                'identifiers' => 'module-management',
                'name' => 'Manajemen Module',
                'url' => '/admin/modules',
                'icon' => 'fa-cubes',
                'row_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
