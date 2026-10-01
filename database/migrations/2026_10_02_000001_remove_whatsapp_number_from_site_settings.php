<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * The company asked to stop publishing the mobile/WhatsApp number. Clearing
 * it hides the number in the contact section and footer and removes the
 * WhatsApp order buttons in the marketplace. It can be re-entered later in
 * Admin → Pengaturan Situs. The column itself is kept.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_settings')) {
            DB::table('site_settings')->update(['whatsapp' => null]);
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: the number is not restored automatically.
    }
};
