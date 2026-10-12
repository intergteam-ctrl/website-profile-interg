<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Instagram API access token (stored encrypted) so the Instagram side panel
 * can pull the latest posts automatically. Refreshed by the app every week.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->text('instagram_access_token')->nullable();
            $table->timestamp('instagram_token_refreshed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['instagram_access_token', 'instagram_token_refreshed_at']);
        });
    }
};
