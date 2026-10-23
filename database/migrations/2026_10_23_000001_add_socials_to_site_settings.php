<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Social media links managed in Admin → Media Sosial instead of code.
 * NULL = never saved yet → the defaults from config/company.php are used.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('socials')->nullable();
            $table->string('youtube_channel_id', 40)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['socials', 'youtube_channel_id']);
        });
    }
};
