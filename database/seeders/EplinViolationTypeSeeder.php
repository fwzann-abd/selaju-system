<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EplinViolationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\EplinViolationType::create([
            'name' => 'Barang Terlarang',
            'slug' => 'barang-terlarang',
            'description' => 'Membawa barang yang tidak boleh dibawa ke sekolah (handphone, gadget, makanan berbahaya, dll)',
        ]);

        \App\Models\EplinViolationType::create([
            'name' => 'Kesiangan / Terlambat',
            'slug' => 'terlambat',
            'description' => 'Hadir ke sekolah setelah jam masuk yang ditentukan',
        ]);

        \App\Models\EplinViolationType::create([
            'name' => 'Tidak Berseragam',
            'slug' => 'tidak-berseragam',
            'description' => 'Tidak mengenakan seragam sesuai dengan peraturan',
        ]);

        \App\Models\EplinViolationType::create([
            'name' => 'Rambut Tidak Sesuai',
            'slug' => 'rambut-tidak-sesuai',
            'description' => 'Rambut tidak sesuai dengan peraturan (terlalu panjang, warna tidak sesuai, dll)',
        ]);

        \App\Models\EplinViolationType::create([
            'name' => 'Tata Tertib Lainnya',
            'slug' => 'tata-tertib-lainnya',
            'description' => 'Pelanggaran tata tertib lainnya yang tidak termasuk kategori di atas',
        ]);
    }
}
