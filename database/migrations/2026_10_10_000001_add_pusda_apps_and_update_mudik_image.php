<?php

use App\Models\Portfolio;
use Illuminate\Database\Migrations\Migration;

/*
 * - New software projects for Dinas PU SDA Jatim: Monitoring PU SDA
 *   (telemetry & river CCTV) and SIBB (flood incident reporting).
 * - Mudik Gratis gets a current (2026) screenshot.
 * Existing records edited by an admin are left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Portfolio::query()
            ->where('slug', 'software-mudik-gratis')
            ->where('image', 'images/profile/app-mudik.jpg')
            ->update(['image' => 'images/profile/app-mudik-2026.jpg']);

        $next = fn (): int => (int) Portfolio::query()->where('group', 'software')->max('sort_order') + 1;

        $monitoring = Portfolio::query()->firstOrCreate(
            ['slug' => 'software-monitoring-pu-sda'],
            [
                'group' => 'software',
                'title' => 'Monitoring PU SDA',
                'subtitle' => 'Dinas PU SDA Prov. Jawa Timur',
                'category' => 'Software & App Development',
                'description' => 'Dashboard pemantauan telemetri dan CCTV sungai di wilayah kerja Dinas PU Sumber Daya Air Provinsi Jawa Timur: ketinggian air dan status setiap titik pantau secara real-time, peta lokasi, serta riwayat perubahan status.',
                'image' => 'images/profile/app-monitoring-pusda.jpg',
                'url' => 'https://metri.dpuair.jatimprov.info/',
                'sort_order' => $next(),
            ],
        );

        Portfolio::query()->firstOrCreate(
            ['slug' => 'software-sibb-sistem-informasi-bencana-banjir'],
            [
                'group' => 'software',
                'title' => 'SIBB — Sistem Informasi Bencana Banjir',
                'subtitle' => 'Dinas PU SDA Prov. Jawa Timur',
                'category' => 'Software & App Development',
                'description' => 'Aplikasi pelaporan banjir yang praktis, cepat, dan tepat di wilayah kerja Dinas PU Sumber Daya Air Provinsi Jawa Timur, dilengkapi peta sebaran kejadian per kabupaten/kota dan daftar kejadian terbaru.',
                'image' => 'images/profile/app-sibb.jpg',
                'sort_order' => $next(),
            ],
        );

        // Fresh installs: the earlier seeder may have created it without the link.
        if ($monitoring->url === null) {
            $monitoring->forceFill(['url' => 'https://metri.dpuair.jatimprov.info/'])->save();
        }
    }

    public function down(): void
    {
        Portfolio::query()->whereIn('slug', ['software-monitoring-pu-sda', 'software-sibb-sistem-informasi-bencana-banjir'])->delete();
        Portfolio::query()->where('slug', 'software-mudik-gratis')
            ->where('image', 'images/profile/app-mudik-2026.jpg')
            ->update(['image' => 'images/profile/app-mudik.jpg']);
    }
};
