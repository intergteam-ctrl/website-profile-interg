<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Projects from "Company Profile Inter G 2025". Idempotent: matches on slug,
 * and never overwrites a record an admin has already edited (only fills
 * records that do not exist yet).
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [];

        foreach (config('company.display.projects', []) as $i => $p) {
            $rows[] = [
                'group' => 'display',
                'title' => $p['client'],
                'category' => $p['type'],
                'subtitle' => $p['tech'],
                'description' => $p['type'].' untuk '.$p['client'].' menggunakan '.$p['tech'].'.',
                'image' => 'images/profile/'.$p['image'],
                'sort_order' => $i + 1,
            ];
        }

        foreach (config('company.apps', []) as $i => $a) {
            $rows[] = [
                'group' => 'software',
                'title' => $a['name'],
                'category' => 'Software & App Development',
                'subtitle' => $a['client'],
                'description' => $a['desc'],
                'image' => 'images/profile/'.$a['image'],
                'url' => $a['url'] ?? null,
                'sort_order' => $i + 1,
            ];
        }

        foreach (config('company.iot.projects', []) as $i => $p) {
            $rows[] = [
                'group' => 'iot',
                'title' => $p['title'],
                'category' => 'IoT Solution',
                'subtitle' => null,
                'description' => $p['desc'],
                'image' => 'images/profile/'.$p['image'],
                'sort_order' => $i + 1,
            ];
        }

        // Older migrations run this seeder before the `url` column exists.
        $hasUrl = Schema::hasColumn('portfolios', 'url');

        foreach ($rows as $row) {
            if (! $hasUrl) {
                unset($row['url']);
            }

            $slug = Str::slug($row['group'].'-'.$row['title']);
            Portfolio::query()->firstOrCreate(['slug' => $slug], $row + ['slug' => $slug]);
        }
    }
}
