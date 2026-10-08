<?php

use App\Models\Portfolio;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('url')->nullable()->after('image');
        });

        // New client app for the "Software & App Development" section.
        // firstOrCreate: never overwrites the record once an admin edits it.
        $mavens = Portfolio::query()->firstOrCreate(
            ['slug' => 'software-mavens-cash-advance-reimbursement'],
            [
                'group' => 'software',
                'title' => 'Mavens Cash Advance & Reimbursement',
                'subtitle' => 'PT Mavens Mitra Perkasa',
                'category' => 'Software & App Development',
                'description' => 'Aplikasi web untuk pengajuan, persetujuan, dan pelaporan cash advance (uang muka) serta reimbursement karyawan PT Mavens Mitra Perkasa.',
                'image' => 'images/profile/app-mavens.jpg',
                'url' => 'https://mavens.interg.co.id/',
                'sort_order' => (int) Portfolio::query()->where('group', 'software')->max('sort_order') + 1,
            ],
        );

        // On a fresh install the earlier seeder already created it without a link.
        if ($mavens->url === null) {
            $mavens->forceFill(['url' => 'https://mavens.interg.co.id/'])->save();
        }
    }

    public function down(): void
    {
        Portfolio::query()->where('slug', 'software-mavens-cash-advance-reimbursement')->delete();

        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
};
