<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        // ===== 1. SITE SETTINGS (singleton, 1 baris) =====
        SiteSetting::query()->firstOrCreate([], []);

        SiteSetting::query()->first()->update([
            'hero_tag' => 'Total IT Solution Provider',
            'hero_title' => "Akselerasi\nDigital 5.0\nTanpa Batas",
            'hero_subtitle' => 'Membantu perusahaan dan pemerintahan bertransformasi melalui solusi teknologi modern — dari system integration, IoT, software development, hingga display solution profesional.',
            'about_text' => null, // kosong = pakai teks dari Company Profile 2025 (config/company.php)
            'services_intro' => 'Solusi teknologi komprehensif yang dirancang khusus untuk kebutuhan pemerintahan dan perusahaan modern.',
            'phone' => '+62 341 400 272',
            'whatsapp' => null,
            'website' => 'interg.co.id',
            'address' => "Perum Permata Jingga Blok AA No. 27,\nTunggulwulung, Lowokwaru, Kota Malang 65143",
        ]);

        // ===== 2. PORTFOLIOS (dari Company Profile 2025) =====
        $this->call(PortfolioSeeder::class);

        // ===== 3. POSTS / BLOG (3 artikel dari halaman statis) =====
        $posts = [
            [
                'title' => 'Masa Depan Smart City: Peran IoT dalam Transformasi Infrastruktur Kota',
                'category' => 'IoT & Smart City',
                'excerpt' => 'Bagaimana sensor IoT, radar traffic, dan telemetri AWLR membentuk ekosistem kota pintar yang efisien dan berkelanjutan...',
                'published_at' => '2025-06-18 09:00:00',
            ],
            [
                'title' => 'Membangun Control Room Modern: Panduan Lengkap Video Wall & Command Center',
                'category' => 'Display Solution',
                'excerpt' => 'Dari pemilihan teknologi display hingga arsitektur sistem yang redundan — kunci membangun ruang kendali operasional kelas enterprise...',
                'published_at' => '2025-06-05 09:00:00',
            ],
            [
                'title' => 'AI Vision Camera: Revolusi Sistem Pengawasan Cerdas untuk Bisnis dan Pemerintah',
                'category' => 'AI & Surveillance',
                'excerpt' => 'Kemampuan analisa, deteksi, identifikasi, dan klasifikasi berbasis AI mengubah paradigma keamanan konvensional menjadi sistem proaktif...',
                'published_at' => '2025-05-22 09:00:00',
            ],
        ];

        foreach ($posts as $data) {
            Post::query()->updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data + [
                    'slug' => Str::slug($data['title']),
                    'content' => '<p>'.$data['excerpt'].'</p>',
                ]
            );
        }
    }
}
