<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Apple App Store link per studio app (+ last good copy of its listing). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('app_store_id', 20)->nullable()->after('play_data');
            $table->json('app_store_data')->nullable()->after('app_store_id');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['app_store_id', 'app_store_data']);
        });
    }
};
