<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('schools')->insert([
            ['id' => Str::uuid(), 'slug' => 'sman-1-jakarta', 'name' => 'SMAN 1 Jakarta'],
            ['id' => Str::uuid(), 'slug' => 'sman-2-bandung', 'name' => 'SMAN 2 Bandung'],
            ['id' => Str::uuid(), 'slug' => 'smk-negeri-1-surabaya', 'name' => 'SMK Negeri 1 Surabaya'],
            ['id' => Str::uuid(), 'slug' => 'sma-islam-al-azhar', 'name' => 'SMA Islam Al-Azhar'],
        ]);
    }
}
