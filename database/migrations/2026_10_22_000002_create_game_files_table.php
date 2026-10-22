<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/*
 * A game is now a folder that can hold several HTML files, each reachable at
 * /games/{folder}/{file-name} (e.g. /games/fruits-bomb/privacy). Existing
 * single-file games are moved into their folder under their original name.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);          // URL name, without ".html"
            $table->longText('html');
            $table->timestamps();
            $table->unique(['game_id', 'name']);
        });

        foreach (DB::table('games')->select(['id', 'html', 'file_name', 'created_at', 'updated_at'])->get() as $game) {
            $name = Str::slug(pathinfo((string) $game->file_name, PATHINFO_FILENAME)) ?: 'index';

            DB::table('game_files')->insert([
                'game_id' => $game->id,
                'name' => $name,
                'html' => (string) $game->html,
                'created_at' => $game->created_at,
                'updated_at' => $game->updated_at,
            ]);
        }

        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['html', 'file_name']);
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->longText('html')->nullable();
            $table->string('file_name')->nullable();
        });

        foreach (DB::table('game_files')->orderBy('id')->get()->unique('game_id') as $file) {
            DB::table('games')->where('id', $file->game_id)->update(['html' => $file->html, 'file_name' => $file->name.'.html']);
        }

        Schema::dropIfExists('game_files');
    }
};
