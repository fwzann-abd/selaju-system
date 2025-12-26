<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create article categories
        $categories = [
            [
                'name' => 'Kegiatan',
                'slug' => 'kegiatan',
                'description' => 'Artikel tentang kegiatan sekolah dan OSIS',
            ],
            [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Artikel tentang teknologi dan inovasi digital',
            ],
            [
                'name' => 'Alumni',
                'slug' => 'alumni',
                'description' => 'Cerita dan artikel dari alumni SMKN 1 Garut',
            ],
        ];

        foreach ($categories as $category) {
            ArticleCategory::create($category);
        }

        // Create sample articles
        $articles = [
            [
                'category_id' => ArticleCategory::where('slug', 'kegiatan')->first()->id,
                'title' => 'Mengelola Organisasi Siswa Era Digital',
                'slug' => 'mengelola-organisasi-siswa-era-digital',
                'content' => 'Dalam era digital ini, mengelola organisasi siswa seperti OSIS memerlukan pendekatan yang inovatif dan terintegrasi dengan teknologi. Artikel ini membahas tips praktis tentang bagaimana memadukan kegiatan OSIS dengan platform digital Selaju untuk meningkatkan efisiensi komunikasi dan kolaborasi antar anggota.',
                'excerpt' => 'Tips praktis memadukan kegiatan OSIS dengan platform digital Selaju.',
                'status' => true,
            ],
            [
                'category_id' => ArticleCategory::where('slug', 'teknologi')->first()->id,
                'title' => 'Roadmap Pelayanan Satu Pintu Sekolah',
                'slug' => 'roadmap-pelayanan-satu-pintu-sekolah',
                'content' => 'Pelayanan satu pintu atau single point of service adalah konsep yang memudahkan siswa dan orang tua mendapatkan berbagai layanan administratif dalam satu platform. Pelajari bagaimana sistem ticketing terintegrasi membantu sekolah merespons kebutuhan dengan lebih cepat dan efisien.',
                'excerpt' => 'Pelajari bagaimana ticketing membantu sekolah merespons lebih cepat.',
                'status' => true,
            ],
            [
                'category_id' => ArticleCategory::where('slug', 'alumni')->first()->id,
                'title' => 'Cerita Alumni yang Menginspirasi',
                'slug' => 'cerita-alumni-yang-menginspirasi',
                'content' => 'Generasi alumni SMKN 1 Garut tahun 2020 memiliki cerita kesuksesan yang menginspirasi. Mereka tidak hanya menjadi profesional di berbagai industri, tetapi juga kembali berkontribusi dalam pengembangan sekolah melalui program mentoring dan kolaborasi strategis.',
                'excerpt' => 'Batch 2020 berbagi cerita karier dan cara mereka kembali berkontribusi.',
                'status' => true,
            ],
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }
    }
}
