<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * categories.image was NOT NULL without a default, but the admin category form
 * has no image field — so creating a category from the panel failed with a
 * SQL integrity error. Nothing on the site reads this column yet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
        });
    }
};
