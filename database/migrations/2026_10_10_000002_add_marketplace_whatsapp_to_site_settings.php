<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Separate WhatsApp number used only for marketplace orders/chat, so the
 * general contact section and footer can stay without a mobile number.
 * Empty = falls back to config('company.marketplace_whatsapp').
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('marketplace_whatsapp')->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('marketplace_whatsapp');
        });
    }
};
