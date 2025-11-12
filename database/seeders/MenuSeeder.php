<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'code' => 'dashboard',
                'name' => 'Dashboard',
                'icon' => 'fa-chart-line',
                'row_order' => 1,
                'status' => true,
            ],
            [
                'code' => 'konten',
                'name' => 'Konten',
                'icon' => 'fa-file-alt',
                'row_order' => 2,
                'status' => true,
            ],
            [
                'code' => 'pengaturan',
                'name' => 'Pengaturan',
                'icon' => 'fa-cog',
                'row_order' => 3,
                'status' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
