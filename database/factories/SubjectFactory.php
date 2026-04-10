<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubjectFactory extends Factory
{
    public function definition(): array
    {
        $subjects = [
            'Matematika',
            'Bahasa Inggris',
            'Bahasa Indonesia',
            'Pemrograman Web',
            'Jaringan Dasar',
            'Desain Grafis',
            'Basis Data',
            'Sistem Operasi',
            'Algoritma',
            'Hardware',
        ];

        $name = $this->faker->randomElement($subjects);

        return [
            'name' => $name,
            'code' => 'SUBJ-'.Str::upper(Str::substr(Str::slug($name), 0, 4)),
        ];
    }
}
