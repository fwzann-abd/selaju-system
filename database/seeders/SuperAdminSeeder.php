<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Super Admin',
            'email' => 'dev@gncs.dev',
            'password' => Hash::make('programmer123'),
            'email_verified_at' => now(),
        ]);
    }
}
