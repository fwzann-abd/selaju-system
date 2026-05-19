<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 *
 * NOTE: school_id and generation_id must be injected by the caller via ->state([...]).
 */
class StudentFactory extends Factory
{
    /** @var array<string> */
    protected static array $maleNames = [
        'Andi', 'Budi', 'Candra', 'Dani', 'Eko', 'Fajar', 'Galih', 'Hendra', 'Irfan', 'Joko',
        'Kevin', 'Lutfi', 'Muhammad', 'Nanda', 'Oscar', 'Panji', 'Rendi', 'Sigit', 'Taufik',
        'Umar', 'Wahyu', 'Yogi', 'Zaky', 'Arief', 'Bayu', 'Dimas', 'Agus', 'Rizki', 'Ilham',
        'Bagus', 'Yusuf', 'Haris', 'Ferdi', 'Gilang', 'Ridwan', 'Surya', 'Faisal', 'Teguh',
    ];

    /** @var array<string> */
    protected static array $femaleNames = [
        'Ayu', 'Bunga', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gadis', 'Hani', 'Indah', 'Kartika',
        'Lia', 'Maya', 'Nadia', 'Putri', 'Rina', 'Sari', 'Tika', 'Ulfa', 'Vina', 'Winda',
        'Yuni', 'Zahra', 'Anisa', 'Bella', 'Dinda', 'Elsa', 'Gita', 'Hesti', 'Intan', 'Jihan',
        'Kania', 'Lestari', 'Mira', 'Nisa', 'Oktavia', 'Puspita', 'Rara', 'Shinta',
    ];

    /** @var array<string> */
    protected static array $lastNames = [
        'Pratama', 'Sari', 'Ramadhan', 'Putri', 'Hidayat', 'Wulandari', 'Saputra', 'Permata',
        'Setiawan', 'Handayani', 'Ardiansyah', 'Amelia', 'Maulana', 'Rahayu', 'Wijaya', 'Anggraini',
        'Nugroho', 'Dewi', 'Hakim', 'Kusuma', 'Rahman', 'Anjani', 'Prasetyo', 'Safitri',
        'Susilo', 'Fitriani', 'Habibi', 'Lestari', 'Firmansyah', 'Purnama', 'Santoso', 'Kurniawan',
        'Apriani', 'Gunawan', 'Herdiansyah', 'Oktaviani', 'Pramudya', 'Suryani',
    ];

    protected static int $studentCounter = 1;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['L', 'P']);
        $firstName = $gender === 'L'
            ? $this->faker->randomElement(self::$maleNames)
            : $this->faker->randomElement(self::$femaleNames);
        $lastName = $this->faker->randomElement(self::$lastNames);

        $counter = static::$studentCounter++;
        $year = 2025;

        return [
            'account_id' => null,
            'school_id' => null,
            'generation_id' => null,
            'name' => "{$firstName} {$lastName}",
            'student_number' => $year.str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'national_id' => '003'.str_pad((string) ($counter + 10000), 7, '0', STR_PAD_LEFT),
            'gender' => $gender,
        ];
    }
}
