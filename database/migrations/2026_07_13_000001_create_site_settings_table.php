<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Hero
            $table->string('hero_tag')->nullable();
            $table->text('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();

            // About
            $table->text('about_text')->nullable();

            // Services
            $table->text('services_intro')->nullable();

            // Contact
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
