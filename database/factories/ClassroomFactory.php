<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClassroomFactory extends Factory
{
    public function definition(): array
    {
        $tingkat = $this->faker->randomElement(['10', '11', '12']);
        $jurusan = $this->faker->randomElement(['PPLG', 'TJKT', 'DKV', 'MPLB', 'AKL', 'BDP']);
        $rombel = $this->faker->randomElement(['1', '2', '3']);
        $name = "$tingkat $jurusan $rombel";

        return [
            'name' => $name,
            'tingkat' => $tingkat,
            'jurusan' => $jurusan,
            'rombel' => $rombel,
            'slug' => Str::slug($name),
            'teacher_id' => Teacher::factory(),
            'academic_year' => '2025/2026',
        ];
    }
}
