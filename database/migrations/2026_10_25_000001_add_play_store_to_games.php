<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * InterG Studio: each folder can be linked to its Google Play listing. The
 * listing (icon, screenshots, description) is fetched from Google Play and
 * the last good copy is kept in play_data as a fallback.
 * Also adds the studio's published apps.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('play_package', 190)->nullable()->after('description');
            $table->json('play_data')->nullable()->after('play_package');
        });

        $apps = [
            ['kubi-puzzle', 'Kubi Puzzle: Offline Games', 'com.interg.kubipuzzle'],
            ['swarm-maze', 'Swarm Maze: Tower Defense', 'com.interg.swarmmaze'],
            ['fruits-bomb', 'Fruits Bomb', 'id.co.interg.fruitsbomb'],
            ['mahjong-academy', 'Mahjong Academy: Learn & Play', 'com.interg.mahjongacademy'],
            ['monster-mess', 'Monster Mess', 'com.mavensplayground.monstermess'],
            ['juragan-dapur-gizi', 'Juragan Dapur Gizi', 'com.juragandapurgizi.app'],
            ['spanwise', 'Bridge Builder: Spanwise', 'com.interg.spanwise'],
            ['mind-matrix', 'IQ Test: Mind Matrix', 'com.interg.iqtest'],
        ];

        foreach ($apps as $i => [$slug, $title, $package]) {
            $existing = DB::table('games')->where('slug', $slug)->orWhere('play_package', $package)->first();

            if ($existing) {
                DB::table('games')->where('id', $existing->id)->update(['play_package' => $package, 'updated_at' => now()]);

                continue;
            }

            DB::table('games')->insert([
                'title' => $title,
                'slug' => $slug,
                'play_package' => $package,
                'is_active' => true,
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['play_package', 'play_data']);
        });
    }
};
