<?php

use Database\Seeders\PortfolioSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Lets the home page sections ("Proyek Display", "Software & App
 * Development") be managed from the admin panel instead of being
 * hard-coded, then loads the Company Profile 2025 projects as initial data
 * so the site keeps showing them right after deploy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('group', 20)->default('other')->after('slug')->index();
            $table->string('subtitle')->nullable()->after('title');
            $table->unsignedInteger('sort_order')->default(0)->after('image');
        });

        (new PortfolioSeeder)->run();
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropIndex(['group']);
            $table->dropColumn(['group', 'subtitle', 'sort_order']);
        });
    }
};
